<?php
namespace Monolog\Handler;

class TestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string $tmpFile
     */
    protected $tmpFile;

    public function setUp()
    {
        parent::setUp();
        $this->tmpFile = sys_get_temp_dir() . '/' . __CLASS__ . '-tmp';
        if (file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
    }

    public function tearDown()
    {
        if (file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
        parent::tearDown();
    }


    protected function getRecord(
        $message = 'test',
        $level = \Monolog\Logger::WARNING,
        $context = array()
    ) {
        return array(
            'message' => $message,
            'context' => $context,
            'level' => $level,
            'level_name' => \Monolog\Logger::getLevelName($level),
            'channel' => 'test',
            'datetime' => \DateTime::createFromFormat(
                'U.u', sprintf('%.6F', microtime(true))
            ),
            'extra' => array(),
        );
    }

    /**
     * Get protected/private member variable.
     * @param object $obj
     * @param string $member
     * @return mixed
     */
    protected function getProp($obj, $member)
    {
        $prop = new \ReflectionProperty($obj, $member);
        $prop->setAccessible(true);
        return $prop->getValue($obj);
    }

    /**
     * Set protected/private member variable.
     * @param object $obj
     * @param string $member
     * @param mixed $value
     */
    protected function setProp($obj, $member, $value)
    {
        $prop = new \ReflectionProperty($obj, $member);
        $prop->setAccessible(true);
        $prop->setValue($obj, $value);
    }
}
