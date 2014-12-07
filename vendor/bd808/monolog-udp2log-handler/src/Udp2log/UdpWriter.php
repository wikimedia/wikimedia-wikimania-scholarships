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

namespace Monolog\Handler\Udp2log;


/**
 * Udp2log message writer.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2014 Bryan Davis and Wikimedia Foundation.
 */
class UdpWriter implements Writer
{
    /**
     * @var string $host
     */
    protected $host;

    /**
     * @var int $port
     */
    protected $port;

    /**
     * @var resource $socket
     */
    protected $socket;


    /**
     * @param string $host Udp2log host
     * @param int $port Udp2log port
     */
    public function __construct($host, $port)
    {
        $this->host = trim((string)$host, '[]');
        $this->port = (int)$port;
        if (filter_var($this->host, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6)) {
            $domain = \AF_INET6;
        } else {
            $domain = \AF_INET;
        }
        $this->socket = socket_create($domain, \SOCK_DGRAM, \SOL_UDP);
    }


    /**
     * @param string $message
     * @param string $prefix Message prefix
     */
    public function write($message, $prefix = null)
    {
        // Clean it up for the multiplexer
        if ($prefix !== null) {
            $message = preg_replace('/^/m', "{$prefix} ", $message);

            // Limit to 64KB
            if (strlen($message) > 65506) {
                $message = substr($message, 0, 65506);
            }

            if (substr($message, -1) !== "\n") {
                $message .= "\n";
            }

        } elseif (strlen($message) > 65507) {
          $message = substr($message, 0, 65507);
          if (substr($message, -1) !== "\n") {
            $message = substr($message, 0, 65506) . "\n";
          }
        }

        $this->send($message);
    }


    /**
     * @param string $buffer
     */
    protected function send($buffer)
    {
        socket_sendto(
            $this->socket, $buffer, strlen($buffer), 0,
            $this->host, $this->port
        );
    }


    public function close()
    {
        if (is_resource($this->socket)) {
            socket_close($this->socket);
        }
        $this->socket = null;
    }
}
