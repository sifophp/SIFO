<?php

namespace Sifo\Mail;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Sifo\Config;

class Mail
{
    /** @var PHPMailer */
    protected $mail;

    /** @var Config */
    protected $config;

    /**
     * @var self
     */
    static private $instance;

    /**
     * Singleton of Client class.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function setDependencies()
    {
        $this->mail = new PHPMailer();
        $this->config = Config::getInstance();
    }

    public function __construct()
    {
        $this->setDependencies();
        $email_properties = $this->config->getConfig('mail');

        foreach ($email_properties as $property => $value) {
            $this->mail->{$property} = $value;
        }
    }

    /**
     * Calls the PHPMailer methods.
     *
     * @param string $method
     * @param mixed $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return \call_user_func_array(array($this->mail, $method), $args);
    }

    /**
     * Get any phpmailer attribute.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->mail->$property;
    }

    /**
     * Set any phpmailer attribute.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $this->mail->$property = $value;
    }

    /**
     * Send an email.
     *
     * @param string $to
     * @param string $subject
     * @param string $body
     *
     * @return void
     * @throws MailException
     * @throws NotSendMailException
     */
    public function send($to, $subject, $body): void
    {
        try {
            $this->mail->Subject = $subject;
            $this->mail->AltBody = strip_tags($body);
            $this->mail->addAddress($to);
            $this->mail->msgHTML($body);

            if (true !== $this->mail->send()) {
                throw new NotSendMailException($this->mail->ErrorInfo);
            }

            $this->mail->clearAddresses();
        } catch (Exception $e) {
            throw new MailException($e->getMessage());
        }
    }
}
