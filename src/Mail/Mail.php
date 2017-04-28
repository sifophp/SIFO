<?php

namespace Sifo\Mail;

use Sifo\Config;

class Mail
{
    /** @var \PHPMailer */
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
    public static function getInstance()
    {
        if (!isset (self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function setDependencies()
    {
        $this->mail = new \PHPMailer();
        $this->config = Config::getInstance();
    }

    public function __construct()
    {
        $this->setDependencies();
        $email_properties = $this->config->getConfig('mail');

        foreach ($email_properties as $property => $value) {
            $this->mail->{$property} = $value;
        }

        return $this->mail;
    }

    /**
     * Calls the PHPmailer methods.
     *
     * @param string $method
     * @param mixed $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->mail, $method), $args);
    }

    /**
     * Get any phpmailer attribute.
     *
     * @param string $property
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
     * @return bool
     * @throws NotSendMailException
     */
    public function send($to, $subject, $body)
    {
        $this->mail->Subject = $subject;
        $this->mail->AltBody = strip_tags($body);
        $this->mail->AddAddress($to);
        $this->mail->MsgHTML($body);

        if (!$this->mail->Send()) {
            throw new NotSendMailException($this->mail->ErrorInfo);
        }

        $this->mail->ClearAddresses();
    }
}
