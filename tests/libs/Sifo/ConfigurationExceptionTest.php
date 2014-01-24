<?php

use Sifo\Exception\ConfigurationException;

/**
 * Test class for ConfigurationException.
 */
class ConfigurationExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigurationException
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ConfigurationException();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
}

