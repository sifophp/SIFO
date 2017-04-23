<?php

namespace Sifo\Mail;

use PHPUnit\Framework\TestCase;
use Sifo\Config;

class MailTest extends TestCase
{
    /** @var \PHPMailer|\PHPUnit_Framework_MockObject_MockObject */
    private $mail;

    /** @var Config|\PHPUnit_Framework_MockObject_MockObject */
    private $config;

    /** @test */
    public function hola()
    {
        $this->havingAPhpMailer();
        $this->havingAConfig();
        $this->whenSendingAnEmail();
    }

    private function havingAPhpMailer()
    {
        $this->mail = $this->getMockBuilder(\PHPMailer::class)->getMock();
        $this->mail->expects($this->once())->method('Send')->willReturn(true);
    }

    private function havingAConfig()
    {
        $this->config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
    }

    private function whenSendingAnEmail()
    {
        $mail = new MailTestClass($this->mail, $this->config);
        $mail->send('donald@trump.com', 'Love message in a bottle', 'Back home, you arrogant mother fucker.');
    }
}

class MailTestClass extends Mail
{
    public function __construct(\PHPMailer $php_mailer, Config $config_component)
    {
        $this->mail = $php_mailer;
        $this->config = $config_component;
    }

    protected function setDependencies()
    {
    }
}
