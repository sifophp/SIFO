<?php

namespace Sifo\Test;

use PHPUnit\Framework\TestCase;
use Sifo\Crypt;
use PHPUnit\Framework\TestCase;

class CryptTest extends TestCase
{
    /** @var string */
    private $text;

    /** @var string */
    private $crypted_text;

    public function tearDown(): void
    {
        $this->text = null;
        $this->crypted_text = null;
    }

    /** @test */
    public function shouldBeAbleToDecryptAPreviouslyCryptedText()
    {
        $this->givenAText();
        $this->whenICryptIt();
        $this->thenDecryptedTextShouldBeEqualThanOriginal();
    }

    /** @test */
    public function shouldBeAbleToDecryptAPreviouslyCryptedTextForUrl()
    {
        $this->givenAText();
        $this->whenICryptItForUrl();
        $this->thenDecryptedTextFromUrlShouldBeEqualThanOriginal();
    }

    private function givenAText()
    {
        $this->text = 'Asereje';
    }

    private function whenICryptIt()
    {
        $this->crypted_text = Crypt::encrypt('Asereje');
    }

    private function thenDecryptedTextShouldBeEqualThanOriginal()
    {
        $this->assertEquals($this->text, Crypt::decrypt($this->crypted_text));
    }

    private function whenICryptItForUrl()
    {
        $this->crypted_text = Crypt::encryptForUrl('Asereje');
    }

    private function thenDecryptedTextFromUrlShouldBeEqualThanOriginal()
    {
        $this->assertEquals($this->text, Crypt::decryptFromUrl($this->crypted_text));
    }
}
