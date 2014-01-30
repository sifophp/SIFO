<?php

use Sifo\Cache;

/**
 * Yea, a mock to override the constructor, awesome.
 */
class CacheMock extends Cache
{
	public function __construct()
	{
	}
}

/**
 * Test class for Cache.
 * Generated by PHPUnit on 2009-11-01 at 12:17:06.
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cache
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new CacheMock;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
	    $this->object = null;
    }

	/**
	 * Test object correct creation.
	 */
	public function testObjectCreation()
	{
		$this->assertTrue( $this->object instanceof Cache );
	}
}


