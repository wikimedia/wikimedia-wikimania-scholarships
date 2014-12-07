<?php
namespace Monolog\Handler\Udp2log;

use Monolog\Handler\TestCase;

/**
 * @coversDefaultClass \Monolog\Handler\Udp2log\UdpWriter
 * @usesDefaultClass \Monolog\Handler\Udp2log\UdpWriter
 * @uses ::<public>
 * @uses ::<private>
 * @uses ::<protected>
 */
class UdpWriterTest extends TestCase
{

    /**
     * @dataProvider provideWriteCallsSend
     * @covers ::write
     */
    public function testWriteCallsSend($message, $prefix, $expect)
    {
        $writer = $this->getMockBuilder(
            '\\Monolog\\Handler\\Udp2log\\UdpWriter')
            ->setMethods(array('send'))
            ->setConstructorArgs(array('127.0.0.1', 9999))
            ->getMock();

        $writer->expects($this->once())
            ->method('send')
            ->with($expect);

        $writer->write($message, $prefix);
    }

    public function provideWriteCallsSend()
    {
        return array(
            array(
                'message',
                null,
                'message',
            ),
            array(
                'message',
                'prefix',
                "prefix message\n",
            ),
            array(
                "foo\nbar\n",
                'prefix',
                "prefix foo\nprefix bar\n",
            ),
            array(
                str_repeat('x', 65508),
                'y',
                'y ' . str_repeat('x', 65504) . "\n",
            ),
            array(
                str_repeat('x', 65508),
                null,
                str_repeat('x', 65506) . "\n",
            ),
        );
    }

    /**
     * @dataProvider provideOpenAndClose
     * @covers ::__construct
     * @covers ::close
     */
    public function testOpenAndClose($host, $port)
    {
        $fixture = new UdpWriter($host, $port);
        $this->assertInternalType(
            'resource',
            $this->getProp($fixture, 'socket')
        );

        $fixture->close();
        $this->assertNull($this->getProp($fixture, 'socket'));
    }

    public function provideOpenAndClose()
    {
        return array(
            array('127.0.0.1', '1234'),
            array('::1', '1234'),
            array('127.0.0.1', 1234),
            array('::1', 1234),
            array('[2001:db8:0:8d3:0:8a2e:70:7344]', 1234),
            array('example.com', 1234),
        );
    }
}
