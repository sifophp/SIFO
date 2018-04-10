<?php

namespace Sifo\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPUnit\Framework\TestCase;
use Sifo\Config;

class MailTest extends TestCase
{
    /** @var PHPMailer|\PHPUnit_Framework_MockObject_MockObject */
    private $mail;

    /** @var Config|\PHPUnit_Framework_MockObject_MockObject */
    private $config;

    public function tearDown()
    {
        $this->config = null;
        $this->mail = null;
    }

    /** @test */
    public function mailShouldCallPhpmailerSendMethod(): void
    {
        $this->havingAPhpMailer();
        $this->havingAConfig();
        $this->thenPhpmailerShouldCallSend();
        $this->whenSendingAnEmail();
    }

    private function havingAPhpMailer(): void
    {
        $this->mail = $this->getMockBuilder(PHPMailer::class)->getMock();
    }

    private function havingAConfig(): void
    {
        $this->config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
    }

    private function whenSendingAnEmail(): void
    {
        $mail = new MailTestClass($this->mail, $this->config);
        $mail->send('donald@trump.com', 'Love message in a bottle', 'Back home, you arrogant mother fucker.');
    }

    private function thenPhpmailerShouldCallSend(): void
    {
        $this->mail->expects($this->once())->method('Send')->willReturn(true);
    }
}

class MailTestClass extends Mail
{
    public function __construct(PHPMailer $php_mailer, Config $config_component)
    {
        $this->mail = $php_mailer;
        $this->config = $config_component;
    }

    protected function setDependencies(): void
    {
    }
}
