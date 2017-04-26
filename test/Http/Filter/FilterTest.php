<?php

namespace Sifo\Http\Filter;

use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    /** @var Filter */
    private $filter;

    /** @var array */
    private $request;

    public function tearDown()
    {
        $this->filter = null;
        $this->request = null;
    }

    public function testFilterPostShouldGetThePostRequest()
    {
        $this->havingAPostRequest();
        $this->whenFilterPostIsCalled();

        $this->assertInstanceOf(FilterPost::class, $this->filter);
        $this->assertEquals($_POST, $this->filter->getRawRequest());
    }

    public function testFilterGetShouldGetTheGetsRequest()
    {
        $this->havingAGetRequest();
        $this->whenFilterGetIsCalled();

        $this->assertInstanceOf(FilterGet::class, $this->filter);
        $this->assertEquals($_GET, $this->filter->getRawRequest());
    }

    public function testFilterCustomShouldGetACustomRequest()
    {
        $this->havingACustomRequest();
        $this->whenFilterCustomIsCalled();

        $this->assertInstanceOf(FilterCustom::class, $this->filter);
        $this->assertEquals($this->request, $this->filter->getRawRequest());
    }

    public function testFilterCustomShouldPreventUsingSingleton()
    {
        $this->expectException(Error::class);

        $this->havingACustomRequest();
        $this->whenFilterCustomIsCalledWithSingleton();
    }

    private function havingAPostRequest()
    {
        $_POST = ['email' => 'post@sifo.me'];
    }

    private function havingAGetRequest()
    {
        $_GET = ['email' => 'get@sifo.me'];
    }


    private function havingACustomRequest()
    {
        $this->request = ['email' => 'custom@sifo.me'];
    }

    private function whenFilterPostIsCalled()
    {
        $this->filter = FilterPost::getInstance();
    }

    private function whenFilterGetIsCalled()
    {
        $this->filter = FilterGet::getInstance();
    }

    private function whenFilterCustomIsCalled()
    {
        $this->filter = new FilterCustom($this->request);
    }

    private function whenFilterCustomIsCalledWithSingleton()
    {
        $this->filter = FilterCustom::getInstance();
    }
}
