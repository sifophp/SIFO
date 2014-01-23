<?php

/**
 * Test class for Exception_Registry.
 */
class Exception_RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Exception_Registry
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Exception_Registry;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
}

