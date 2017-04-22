<?php

namespace Sifo;

class Mail
{
    /**
     * @var \PHPMailer
     */
    protected $mail;

    /**
     * @var self
     */
    static private $instance;

    /**
     * Singleton of Client class.
     *
     * @param string $instance_name Instance Name, needed to determine correct paths.
     *
     * @return object Client
     */
    public static function getInstance()
    {
        if (!isset (self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $config = Config::getInstance()->getConfig('mail');

        $this->mail           = new \PHPMailer();
        $this->mail->CharSet  = $config['CharSet'];
        $this->mail->From     = $config['From'];
        $this->mail->FromName = $config['FromName'];

        foreach ($config as $property => $value)
        {
            $this->mail->$property = $value;
        }

        return $this->mail;
    }


    /**
     * Calls the PHPmailer methods.
     *
     * @param string $method
     * @param mixed  $args
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
     * @param mixed  $value
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
     * @return boolean
     */
    public function send($to, $subject, $body)
    {
        $this->mail->Subject = $subject;
        $this->mail->AltBody = strip_tags($body);
        $this->mail->AddAddress($to);
        $this->mail->MsgHTML($body);

        if (!$this->mail->Send())
        {
            trigger_error($this->mail->ErrorInfo);

            return false;
        }

        $this->mail->ClearAddresses();

        return true;
    }
}
