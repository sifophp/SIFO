<?php

namespace Sifo;

use PHPUnit\Framework\Error\Warning;
use PHPUnit\Framework\TestCase;
use Sifo\Exception\RegistryException;

class RegistryTest extends TestCase
{
    /** @var string */
    private $key;

    /** @var mixed */
    private $value;

    public function tearDown()
    {
        $this->key = null;
        $this->value = null;
    }

    /** @test */
    public function shouldStoreAndRetrieveASimpleValue()
    {
        $this->givenASimpleValue();
        $this->whenIStoreAValueInRegistry();
        $this->thenAValueShouldExistInRegistry();
        $this->andAValueShouldBeRecoveredFromRegistry();
    }

    /** @test */
    public function shouldRaiseAWarningIfTryToGetUnexistentKey()
    {
        $this->expectException(Warning::class);
        $this->expectExceptionMessage('Registry doesn\'t contain any element named');

        $this->whenIRecoverANonExistentKeyFromRegistry();
    }

    /** @test */
    public function shouldBeAbleToPushSomeValuesToSomeKey()
    {
        $this->givensomeValues();
        $this->whenIPushEveryValue();
        $this->thenAValueShouldExistInRegistry();
        $this->andAValueShouldBeRecoveredFromRegistry();
    }

    /** @test */
    public function shouldNotBeAbleToPushSomeValuesToAKeyThatIsNotAnArray()
    {
        $this->expectException(RegistryException::class);
        $this->expectExceptionMessage('Failed to PUSH an element in the registry');

        $this->givenASimpleValue();
        $this->whenIPushARandomValue();
    }

    /** @test */
    public function shouldInvalidateAKey()
    {
        $this->givenASimpleValue();
        $this->whenIStoreAValueInRegistry();
        $this->thenAValueShouldExistInRegistry();
        $this->andWhenIInvalidateTheKey();
        $this->thenAValueShouldNotExistInRegistry();
    }

    /** @test */
    public function shouldWorkWithSingleton()
    {
        $this->givenASimpleValue();
        $this->whenIStoreAValueInRegistryUsingSingleton();
        $this->thenAValueShouldExistInRegistry();
        $this->andAValueShouldBeRecoveredFromRegistry();
    }

    private function givenASimpleValue()
    {
        $this->key = 'simple_key';
        $this->value = 'A simple value';
    }

    private function givensomeValues()
    {
        $this->key = 'array_key';
        $this->value = ['one', 'two', 'three'];
    }

    private function whenIStoreAValueInRegistry()
    {
        Registry::set($this->key, $this->value);
    }

    private function whenIStoreAValueInRegistryUsingSingleton()
    {
        $registry = Registry::getInstance();
        $registry->set($this->key, $this->value);
    }

    private function thenAValueShouldExistInRegistry()
    {
        $this->assertTrue(Registry::keyExists($this->key));
    }

    private function thenAValueShouldNotExistInRegistry()
    {
        $this->assertFalse(Registry::keyExists($this->key));
    }

    private function andAValueShouldBeRecoveredFromRegistry()
    {
        $this->assertEquals($this->value, Registry::get($this->key));
    }

    private function whenIRecoverANonExistentKeyFromRegistry()
    {
        Registry::get('whatever');
    }

    private function whenIPushEveryValue()
    {
        foreach ($this->value as $value) {
            Registry::push($this->key, $value);
        }
    }

    private function whenIPushARandomValue()
    {
        Registry::push($this->key, 'random_value');
    }

    private function andWhenIInvalidateTheKey()
    {
        Registry::invalidate($this->key);
    }
}
