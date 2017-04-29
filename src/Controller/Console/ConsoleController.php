<?php

namespace Sifo\Controller\Console;

use Sifo\Bootstrap;
use Sifo\CLBootstrap;
use Sifo\Controller\Controller;
use Sifo\Http\Domains;
use Sifo\Http\Filter\FilterServer;
use Sifo\Mail\Mail;

abstract class ConsoleController extends Controller
{
    const TEST = 'TEST';
    const VERBOSE = 'VERBOSE';
    const ALL = 'INFO';

    /*
     * Used to avoid send empty mails. Should be the number of lines constant in every script execution.
     *
     */
    const MAX_LINES_WITHOUT_SEND_MAIL = 2; // Thes start and end time.

    private $_verbose = false;
    protected $recipient;
    private $_stdout = '';
    private $_script_name;
    private $_domain_name;
    public $debug_mode = false;
    public $test = false;
    public $command_options;
    public $help_str = "Use 'php script-name domain.ext <options>' (SIFO default help string. Redefine this property for customize this message.)";
    public $force = false;

    /**
     * Send the STDOUT to the following recipients.
     *
     * @var array
     */
    protected $recipient_list = array();

    /**
     * Foreground colors for shell messages.
     *
     * @var array
     */
    public $_foreground_colors = array(
        'black' => '0;30',
        'blue' => '0;34',
        'green' => '0;32',
        'cyan' => '0;36',
        'red' => '0;31',
        'purple' => '0;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'white' => '1;37'
    );

    /**
     * Background colors for shell messages.
     *
     * @var array
     */
    public $_background_colors = array(
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'gray' => '47'
    );

    /**
     * Shell params array.
     *
     * @var array
     */
    public $_shell_common_params = array(
        array(
            'short_param_name' => 'h',
            'long_param_name' => 'help',
            'help_string' => 'Show this screen.',
            'need_second_param' => false,
            'is_required' => false,
        ),
        array(
            'short_param_name' => 't',
            'long_param_name' => 'test',
            'help_string' => 'Test mode on.',
            'need_second_param' => false,
            'is_required' => false,
        ),
        array(
            'short_param_name' => 'v',
            'long_param_name' => 'verbose',
            'help_string' => 'Active the verbose mode',
            'need_second_param' => false,
            'is_required' => false,
        ),
        array(
            'short_param_name' => 'r',
            'long_param_name' => 'recipient',
            'help_string' => 'Email address or addresses (separated by comma [,]) to send the output.',
            'need_second_param' => true,
            'is_required' => false,
        ),
        array(
            'short_param_name' => 'f',
            'long_param_name' => 'force',
            'help_string' => 'Run the script without another instance in execution validation.',
            'need_second_param' => false,
            'is_required' => false,
        ),
        array(
            'short_param_name' => 'dm',
            'long_param_name' => 'debugmode',
            'help_string' => 'Valid values: 1 to activate debug, 0 to deactivate debug. If it\'s active it will generate the debug output in /sifo/logs/',
            'need_second_param' => true,
            'is_required' => false,
        ),
    );

    abstract function init();

    abstract function exec();

    public function __construct()
    {
        $this->instance = CLBootstrap::$instance;
        $this->language = Domains::getInstance()->getLanguage();

        $this->params = array(
            'instance' => Bootstrap::$instance,
            'controller' => get_class($this),
            'has_debug' => Domains::getInstance()->getDebugMode(),
            'lang' => $this->language,
        );

        if (extension_loaded('newrelic')) {
            newrelic_name_transaction(get_class($this));
        }

        $this->debug_mode = Domains::getInstance()->getDebugMode();

        // Init i18n configuration.
        $this->i18n = \Sifo\I18N::getInstance(Domains::getInstance()->getLanguageDomain(), $this->language);
    }

    /**
     * Private domain name property "getter".
     *
     * @return mixed
     */
    protected function getDomainName()
    {
        return $this->_domain_name;
    }

    /**
     * Print a message on the console.
     *
     * Usage example:
     *
     * $this->showMessage( 'Example message', self::VERBOSE, array( 'background' => 'red', 'indent' => 4 ) );
     *
     * @param string $message
     * @param object|string $in_mode (by default: self::ALL)
     * @param array $params (optional array keys: indent, foreground and background)
     * @throws \OutOfBoundsException
     */
    protected function showMessage($message, $in_mode = self::ALL, $params = null)
    {
        if (isset($params) && is_array($params)) {
            $color_codes = '';
            $tabs = '';

            foreach ($params as $key => $value) {
                switch ($key) {
                    case 'foreground': {
                        $color_codes .= "\033[" . $this->_foreground_colors[$value] . "m";
                    }
                        break;

                    case 'background': {
                        $color_codes .= "\033[" . $this->_background_colors[$value] . "m";
                    }
                        break;

                    case 'indent': {
                        for ($i = 0; $i < $value; $i++) {
                            $tabs = "\t" . $tabs;
                        }
                    }
                        break;
                }
            }
            $message = $color_codes . "[" . $in_mode . "] " . $tabs . $message . "\033[0m";
        } else {
            $message = "[" . $in_mode . "] " . $message;
        }

        switch ($in_mode) {
            case self::TEST:
                if ($this->test) {
                    $this->addToOutput($message);
                    echo $message . PHP_EOL;
                }
                break;
            case self::VERBOSE:
                $this->addToOutput($message);
                if ($this->_verbose) {
                    echo $message . PHP_EOL;
                }
                break;
            case self::ALL:
                $this->addToOutput($message);
                echo $message . PHP_EOL;
                break;
            default:
                throw new \OutOfBoundsException('Undefined in_mode selected.');
        }
    }

    protected function addToOutput($message_line)
    {
        $this->_stdout .= $message_line . PHP_EOL;
    }

    /**
     * Set a new exec param.
     *
     * @param string $short_param_name The short option id.
     * @param string $long_param_name The long name option.
     * @param string $help_string The help string.
     * @param boolean $need_second_param True if needs a param.
     * @param boolean $is_required Must be set.
     * @throws \RuntimeException
     */
    protected function setNewParam($short_param_name, $long_param_name, $help_string, $need_second_param, $is_required)
    {
        foreach ($this->_shell_common_params as $param) {
            if (($short_param_name == $param['short_param_name']) || ($long_param_name == $param['long_param_name'])) {
                throw new \RuntimeException('You are trying to set a previously defined param.');
            }
        }

        $this->_shell_common_params[] = array(
            'short_param_name' => $short_param_name,
            'long_param_name' => $long_param_name,
            'help_string' => $help_string,
            'need_second_param' => $need_second_param,
            'is_required' => $is_required,
        );

    }

    public function showHelp()
    {
        echo PHP_EOL . $this->help_str . PHP_EOL . PHP_EOL;
        foreach ($this->_shell_common_params as $param) {
            echo '--' . $param['long_param_name'] . "(-" . $param['short_param_name'] . ")\t:";
            if ($param['is_required']) {
                echo "(REQUIRED) ";
            }
            echo $param['help_string'];
            if ($param['need_second_param']) {
                echo " (use with a value like '--" . $param['long_param_name'] . " value')";
            }
            echo PHP_EOL . PHP_EOL;
        }
    }

    private function _getParams()
    {
        $i = -1;
        $params = array();
        if ($argv = FilterServer::getInstance()->getArray('argv')) {
            foreach ($argv as $option) {
                if (preg_match("/^--(\w+)/", $option, $matchs)) {
                    $params[++$i][0] = $matchs[1];
                } else {
                    if (preg_match("/^-(\w+)/", $option, $matchs)) {
                        $params[++$i][0] = $matchs[1];
                    } else {
                        if ($i > -1) {
                            $params[$i++][1] = $option;
                        }
                    }
                }
            }
        }
        $this->command_options = $params;
    }

    private function _validateParams()
    {
        foreach ($this->command_options as $option) {
            $found = false;
            foreach ($this->_shell_common_params as $defined_option) {
                if (($option[0] == $defined_option['short_param_name']) || ($option[0] == $defined_option['long_param_name'])) {
                    $found = true;
                    if ($defined_option['need_second_param']) {
                        if (!isset($option[1])) {
                            $this->showMessage("Need define a param in for use '$option[0]' option.");
                            $this->showHelp();
                            return false;
                        }
                    }
                    break;
                }
            }

            if (!$found) {
                $message = "Error in command options. Unknown option: '$option[0]'.";

                // Check if there are any other similar options and suggest them
                if (false != $closest_option = $this->_getClosestOptions($option[0])) {
                    $message .= " Did you mean one of the following available options?:";

                    foreach ($closest_option as $option) {
                        $message .= "\n$option";
                    }
                }

                $this->showMessage($message);
                $this->showHelp();
                return false;
            }
        }

        // Validating required options:
        foreach ($this->_shell_common_params as $defined_option) {
            if ($defined_option['is_required']) {
                $found = false;
                foreach ($this->command_options as $option) {
                    if (($option[0] == $defined_option['short_param_name']) || ($option[0] == $defined_option['long_param_name'])) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $this->showMessage("Error: '" . $defined_option["long_param_name"] . "' required option not found.");
                    $this->showHelp();
                    return false;
                }
            }
        }

        $argv = FilterServer::getInstance()->getArray('argv');
        preg_match("/([^\/]+)$/", $argv[0], $matchs);
        $this->_script_name = $matchs[0];
        $this->_domain_name = $argv[1];

        return true;
    }

    private function _validateCommandCall()
    {
        if (!($this instanceof self)) {
            $this->showMessage('For make a script runnable controller, these must be instance of SharedCommandLineController');
            return false;
        }
        return true;
    }

    private function _common_exec()
    {
        foreach ($this->command_options as $option) {
            switch ($option[0]) {
                case "h":
                case "help":
                    $this->showHelp();
                    die;
                case "v":
                case "verbose":
                    $this->_verbose = true;
                    break;
                case "t":
                case "test":
                    $this->test = true;
                    break;
                case "r":
                case "recipient":
                    $this->recipient = explode(',', $option[1]);
                    break;
                case "f":
                case "force":
                    $this->force = true;
                    break;
                case "dm":
                case "debugmode":
                    $this->params['has_debug'] = (bool)$option[1];
                    Domains::getInstance()->setDebugMode((bool)$option[1]);
                    break;
            }
        }
    }

    /**
     * Returns the subject of the email.
     */
    protected function getSubject()
    {
        return 'STDOUT ' . $this->_script_name . ' in ' . $this->_domain_name . ' at ' . date('Y-m-d');
    }

    private function _reformatToEmail($content)
    {
        // Find color codes into $content and change it for css style
        $foreground_color = 'black';
        $backround_color = 'white';

        $content_lines = explode("\n", $content);
        $reformated_content = '';

        foreach ($content_lines as $line) {
            foreach ($this->_foreground_colors as $key => $value) {
                $line = str_replace("[" . $value . "m", "<span style='color:$key'>", $line);
            }

            foreach ($this->_background_colors as $key => $value) {
                $line = str_replace("[" . $value . "m", "<span style='background-color:$key'>", $line);
            }

            // Replace [back&fore]ground colors close tags
            $line = str_replace("[0m", '', $line);   // We don't want any command line color close tag
            $spans_to_close = substr_count($line, "<span"); // We have to know how many spans we've opened
            for ($i = 0; $i < $spans_to_close; $i++)   // For each of those opened spans...
            {
                $line .= "</span>";
            }

            $reformated_content .= $line . "<br />\n";   // Implode each line with a line break at the end
        }
        return str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $reformated_content); //indent and return it
    }

    protected function sendMail()
    {
        if (isset($this->recipient) || !empty($this->recipient_list)) {
            if (!empty($this->recipient)) {
                $this->recipient_list = array_merge($this->recipient_list,
                    array_diff($this->recipient, $this->recipient_list));
            }

            if (self::MAX_LINES_WITHOUT_SEND_MAIL < (count(explode(PHP_EOL, $this->_stdout)) - 1)) {
                $mail = new Mail();
                $subject = $this->getSubject();
                $content = $this->_reformatToEmail($this->_stdout);

                foreach ($this->recipient_list as $recipient) {
                    $this->showMessage("Now I would try send an email with subject: '" . $subject . "' to '" . $recipient . "'",
                        self::TEST);
                    if (!$this->test) {
                        $mail->send($recipient, $subject, $content);
                    }
                }
            } else {
                $this->showMessage("Unsent email because the script output was empty.");
            }
        }
    }

    private function _startScript()
    {
        $this->showMessage('Script ' . $this->_script_name . ' in ' . $this->_domain_name . ' started at:' . date('d-M-Y H:i:s'));
        $this->showMessage('* Running in TEST MODE.', self::TEST);
        $this->showMessage('* Running in VERBOSE MODE.', self::VERBOSE);
    }

    private function _stopScript()
    {
        $this->showMessage('Finished at: ' . date('d-M-Y H:i:s'));
    }

    private function _validateScriptRunning()
    {
        if ($this->force) {
            $this->showMessage('Running without another instance execution validation.');
            return true;
        }
        $my_pid = getmypid();
        $pids = [];
        $command = "ps -eo pid,args| grep \"$this->_script_name $this->_domain_name\" | grep -v grep| grep -v /sh| grep -v $my_pid | cut -f2 -d\" \"";
        exec($command, $pids, $err);
        if ($err) {
            $this->showMessage('Error trying to search another instance execution. Run with -f option.');
            return false;
        }
        if (count($pids) > 0) {
            $this->showMessage("Is running another instance of '$this->_script_name $this->_domain_name'. Wait until finish, use -f for force or run 'kill -9 " . implode(' ',
                    $pids) . "' for assassinate it.");
            return false;
        }
        $this->showMessage("There are not other running instances", self::VERBOSE);
        return true;
    }

    public function build()
    {
        $this->_startScript();
        $this->init();
        $this->_getParams();
        if ($this->_validateCommandCall() && $this->_validateParams()) {
            $this->_common_exec();
            if ($this->_validateScriptRunning()) {
                $this->parseParams();
                $this->exec();
            }
        }
        $this->_stopScript();
        $this->sendMail();
    }

    /**
     * Parse the input arguments and store them in a class property for later usage.
     *
     * @internal param array $params Get params.
     * @return array
     */
    protected function parseParams()
    {
        $this->params['parsed_params'] = array();
        foreach ($this->_shell_common_params as $common_param) {
            $value = false;
            foreach ($this->command_options as $option) {
                if ($option[0] === $common_param['short_param_name'] || $option[0] === $common_param['long_param_name']) {
                    if (!isset($option[1])) {
                        $value = true;
                    } else {
                        $value = $option[1];
                    }
                    break;
                }
            }

            $this->params['parsed_params'][$common_param['short_param_name']] = $value;
            $this->params['parsed_params'][$common_param['long_param_name']] = $value;
        }
    }

    /**
     * Returns the closest options to the received $undefined_option based on the $this->_shell_common_params array
     * Script example call: .../scripts/amazon-s3-uploader.php your.instance --dest
     * Method input: dest
     * Return: [test, destination_uri, destination_bucket]
     *
     * @param $undefined_option string
     *
     * @return array
     */
    private function _getClosestOptions($undefined_option)
    {
        $long_param_names = array();

        // TODO: Substitute this loop by an array_column() method call in PHP5.5 happy environments
        foreach ($this->_shell_common_params as $option) {
            $long_param_names[] = $option['long_param_name'];
        }

        return $this->_getClosestWords($undefined_option, $long_param_names);
    }

    /**
     * Returns similar words to $unknown_word among $possible_words.
     * Input example: dest, [help, test, verbose, ..., destination_bucket, destination_uri, acl]
     * Return: [test, destination_uri, destination_bucket]
     *
     * @param $unknown_word string
     * @param $possible_words array
     *
     * @return array
     */
    private function _getClosestWords($unknown_word, $possible_words)
    {
        $alternatives = array();

        foreach ($possible_words as $possible_value) {
            $levenshtein_score = levenshtein($unknown_word, $possible_value);

            if ($levenshtein_score <= strlen($unknown_word) / 2 || false !== strpos($possible_value, $unknown_word)) {
                $alternatives[$possible_value] = $levenshtein_score;
            }
        }

        asort($alternatives);

        return array_keys($alternatives);
    }
}
