<?php
/**
 * @section LICENSE
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Bryan Davis and Wikimedia Foundation
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including without
 * limitation the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit persons to
 * whom the Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @file
 */

namespace Monolog\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\Udp2log\StreamWriter;
use Monolog\Handler\Udp2log\UdpWriter;
use Monolog\Handler\Udp2log\Writer;
use Monolog\Logger;


/**
 * Log handler that replicates the behavior of MediaWiki's wfErrorLog()
 * logging service. Log output can be directed to a local file, a PHP stream,
 * or a udp2log server.
 *
 * For udp2log output, the stream specification must have the form:
 * "udp://HOST:PORT[/PREFIX]"
 * where:
 * - HOST: IPv4, IPv6 or hostname
 * - PORT: server port
 * - PREFIX: optional (but recommended) prefix telling udp2log how to route
 * the log event
 *
 * When not targeting a udp2log stream this class will act as a drop-in
 * replacement for Monolog's StreamHandler.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2014 Bryan Davis and Wikimedia Foundation.
 */
class Udp2logHandler extends AbstractProcessingHandler
{
    /**
     * Log destination descriptor
     * @var string $uri
     */
    protected $uri;

    /**
     * @var Writer $writer
     */
    protected $writer;

    /**
     * @var string $error
     */
    protected $error;

    /**
     * @var string $prefix
     */
    protected $prefix;


    /**
     * @param string $stream Stream URI
     * @param int $level Minimum logging level that will trigger handler
     * @param bool $bubble Can handled meesages bubble up the handler stack?
     */
    public function __construct(
        $stream,
        $level = Logger::DEBUG,
        $bubble = true
    ) {
        parent::__construct($level, $bubble);
        $this->uri = $stream;
    }


    /**
     * Open the log sink described by our stream URI.
     */
    protected function openSink()
    {
        if (!$this->uri) {
            throw new \LogicException(
                'Missing stream uri, the stream can not be opened.'
            );
        }
        $this->error = null;
        set_error_handler(array($this, 'errorTrap'));

        if (substr($this->uri, 0, 4) === 'udp:') {
            $parsed = parse_url($this->uri);
            if (!isset($parsed['host'])) {
                throw new \UnexpectedValueException(sprintf(
                    'Udp transport "%s" must specify a host', $this->uri
                ));
            }
            if (!isset($parsed['port'])) {
                throw new \UnexpectedValueException(sprintf(
                    'Udp transport "%s" must specify a port', $this->uri
                ));
            }

            $this->prefix = '';
            if (isset($parsed['path'])) {
                $this->prefix = ltrim($parsed['path'], '/');
            }

            $this->writer = new UdpWriter($parsed['host'], $parsed['port']);
        } else {
            $this->writer = new StreamWriter($this->uri);
        }
        restore_error_handler();

        if ($this->error !== null) {
            $this->writer = null;
            throw new \UnexpectedValueException(sprintf(
                'The stream or file "%s" could not be opened: %s',
                $this->uri, $this->error
            ));
        }
    }


    /**
     * Custom error handler.
     * @param int $code Error number
     * @param string $msg Error message
     */
    public function errorTrap($code, $msg)
    {
        $this->error = $msg;
    }


    protected function write(array $record)
    {
        if ($this->writer === null) {
            $this->openSink();
        }

        $text = (string) $record['formatted'];
        $this->writer->write($text, $this->prefix);
    }


    public function close()
    {
        if ($this->writer !== null) {
            $this->writer->close();
        }
        $this->writer = null;
    }
}
