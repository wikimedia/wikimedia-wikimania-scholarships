<?php
namespace Monolog\Handler;

/**
 * @coversDefaultClass \Monolog\Handler\Udp2logHandler
 * @usesDefaultClass \Monolog\Handler\Udp2logHandler
 * @uses ::<public>
 * @uses ::<private>
 * @uses ::<protected>
 */
class Udp2logHandlerTest extends TestCase
{

    /**
     * @var Udp2logHandler $fixture
     */
    protected $fixture;

    public function tearDown()
    {
        if ($this->fixture !== null) {
            $this->fixture->close();
        }
        parent::tearDown();
    }


    /**
     * @expectedException \LogicException
     * @covers ::__construct
     * @covers ::openSink
     */
    public function testNullStreamThrows()
    {
        $this->fixture = new Udp2logHandler(null);
        $this->fixture->handle($this->getRecord('message'));
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage host
     * @covers ::__construct
     * @covers ::openSink
     * @covers ::errorTrap
     */
    public function testUdpUriMissingHostThrows()
    {
        $this->fixture = new Udp2logHandler('udp://');
        $this->fixture->handle($this->getRecord('message'));
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage port
     * @covers ::__construct
     * @covers ::openSink
     * @covers ::errorTrap
     */
    public function testUdpUriMissingPortThrows()
    {
        $this->fixture = new Udp2logHandler('udp://127.0.0.1');
        $this->fixture->handle($this->getRecord('message'));
    }

    /**
     * @covers ::__construct
     * @covers ::openSink
     * @covers ::write
     * @uses \Monolog\Handler\Udp2log\UdpWriter
     */
    public function testUdpUriCreatesUdpWriter()
    {
        $this->fixture = new Udp2logHandler('udp://127.0.0.1:123/foo');
        $this->fixture->handle($this->getRecord('message'));

        $this->assertEquals('foo', $this->getProp($this->fixture, 'prefix'));

        $writer = $this->getProp($this->fixture, 'writer');
        $this->assertInstanceOf(
            '\\Monolog\\Handler\\Udp2log\\UdpWriter',
            $writer
        );
    }

    /**
     * @covers ::__construct
     * @covers ::openSink
     * @covers ::write
     * @uses \Monolog\Handler\Udp2log\StreamWriter
     */
    public function testFileUriCreatesStreamWriter()
    {
        $this->fixture = new Udp2logHandler($this->tmpFile);
        $this->fixture->handle($this->getRecord('message'));

        $this->assertNull($this->getProp($this->fixture, 'prefix'));

        $writer = $this->getProp($this->fixture, 'writer');
        $this->assertInstanceOf(
            '\\Monolog\\Handler\\Udp2log\\StreamWriter',
            $writer
        );
        $this->assertFileExists($this->tmpFile);
    }

    /**
     * @covers ::__construct
     * @covers ::write
     * @covers ::close
     */
    public function testDeligatesToWriter()
    {
        $writer = $this->getMock('\Monolog\Handler\Udp2log\Writer');
        $writer->expects($this->at(0))
            ->method('write')
            ->with('message', 'prefix');
        $writer->expects($this->at(1))
            ->method('close');

        $this->fixture = new Udp2logHandler('');
        $this->fixture->setFormatter(
            new \Monolog\Formatter\LineFormatter('%message%')
        );
        // Inject our mock writer
        $this->setProp($this->fixture, 'writer', $writer);
        $this->setProp($this->fixture, 'prefix', 'prefix');

        $this->fixture->handle($this->getRecord('message'));
        $this->fixture->close();
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage could not be opened
     * @covers ::__construct
     * @covers ::errorTrap
     * @covers ::openSink
     * @covers ::write
     * @uses \Monolog\Handler\Udp2log\StreamWriter
     */
    public function testReadOnlyFileThrows()
    {
        $fh = fopen($this->tmpFile, 'w');
        fwrite($fh, '');
        fclose($fh);
        chmod($this->tmpFile, 0444);

        $this->fixture = new Udp2logHandler($this->tmpFile);
        $this->fixture->handle($this->getRecord('message'));
    }
}
