<?php

namespace SifoTest\Filter;

use Sifo\Filter\Post;

class PostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Post
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $_POST        = [
            'name'    => 'John Appleseed',
            'email'   => 'john.appleseed@apple.com',
            'address' => ['street' => 'Smith St.', 'city' => 'London'],
            'age'     => 37,
            'height'  => 72.5,
            'ip'      => '192.168.1.1',
            'url'     => 'http://www.apple.com',
            'valid'   => true
        ];
        $this->object = Post::getInstance();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /** @test */
    public function should_return_a_filter_post()
    {
        $this->assertInstanceOf(Post::class, $this->object);
    }

    /** @test */
    public function should_detect_sent_variables()
    {
        $this->assertTrue($this->object->isSent('name'));
        $this->assertFalse($this->object->isSent('unknown'));
    }

    /** @test */
    public function should_count_variables()
    {
        $expected_count = count($_POST);
        $this->assertEquals($expected_count, $this->object->countVars());
    }

    /** @test */
    public function should_get_string()
    {
        $this->assertInternalType('string', $this->object->getString('name'));
        $this->assertFalse($this->object->getString('address'));
    }

    /** @test */
    public function should_get_unfiltered()
    {
        $this->assertEquals($_POST['address'], $this->object->getUnfiltered('address'));
    }

    /** @test */
    public function should_get_email()
    {
        $this->assertInternalType('string', $this->object->getEmail('email'));
        $this->assertFalse($this->object->getEmail('name'));
    }

    /** @test */
    public function should_get_boolean()
    {
        $this->assertTrue($this->object->getBoolean('valid'));
    }

    /** @test */
    public function should_get_float()
    {
        $this->assertInternalType('float', $this->object->getFloat('height'));
    }


    /** @test */
    public function should_get_integer()
    {
        $this->assertInternalType('integer', $this->object->getInteger('age'));
    }

    /** @test */
    public function should_get_ip()
    {
        $this->assertFalse($this->object->getIP('name'));
        $this->assertNotFalse($this->object->getIP('ip'));
    }

    /** @test */
    public function testGetRegexp()
    {
        $this->assertNotFalse($this->object->getRegexp('name', '/[a-z ]/i'));
    }

    /** @test */
    public function should_get_valid_url()
    {
        $this->assertFalse($this->object->getUrl('name'));
        $this->assertNotFalse($this->object->getUrl('url'));
    }

    /** @test */
    public function should_get_array()
    {
        $this->assertInternalType('array', $this->object->getArray('address'));
    }

    /** @test */
    public function should_get_raw_request()
    {
        $this->assertSame($_POST, $this->object->getRawRequest());
    }
}

