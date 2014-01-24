<?php

use Sifo\Exception\RegistryException;

/**
 * Test class for RegistryException.
 */
class RegistryExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegistryException
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new RegistryException();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
}

