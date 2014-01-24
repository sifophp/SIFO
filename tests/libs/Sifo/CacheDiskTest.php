<?php

use \Sifo\Cache\CacheDisk as CacheDisk;
/**
 * Test class for CacheDisk.
 * Generated by PHPUnit on 2009-11-01 at 12:17:06.
 */
class CacheDiskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CacheDisk
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new CacheDisk;
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
		$this->assertTrue( $this->object instanceof CacheDisk );
	}

}

