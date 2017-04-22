<?php

namespace Sifo;

/**
 * Form utilites to manage data validation.
 *
 * You can add elements by using any of the functions:
 * validateSingleElements
 * validateSingleElement
 * validateElements
 *
 * Examples using validateElements:
 *
 * Only fields 'name' and 'filter' are required. Examples:
 *
 * $form_elems[] = array(
 *    'name' => 'email',  // Name of the input
 *    'filter' => 'Email', // Filter rule to apply, e.g: 'Email' for Filter::getEmail function
 *    'params' => false, // Additional parameters needed by the filtering function.
 *    'required' => true // The field is required to continue?
 *    );
 *
 * // Validate an integer between 16 and 150
 * $form_elems[] = array(
 *     'name' => 'age',
 *     'filter' => 'Integer',
 *     'params' => array( 16, 150 )
 *    );
 *
 * // Validate a username with a regular expression
 * $form_elems[] = array(
 *     'name' => 'username',
 *     'filter' => 'Regexp',
 *     'params' => array( '/[a-z]+/' ),
 *     'error' => 'Only letters are accepted in the username.'
 *    );
 */
class Form
{
    /**
     * Singleton keeper.
     *
     * @var Form
     */
    protected static $instance;

    /**
     * Filter object.
     *
     * @var Filter
     */
    protected $filter;

    /**
     * Fields added to form.
     *
     * @var array
     */
    protected $fields = array();

    /**
     * Errors parsed so far.
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Flag checking if form is valid.
     *
     * @var boolean
     */
    protected $is_valid = true;

    /**
     * @param Filter $filter Filter object (FilterPost, FilterGet...)
     *
     * @return Form
     */
    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Singleton
     *
     * @param Filter $filter Filter object (FilterPost, FilterGet...)
     *
     * @return Form
     */
    static public function getInstance(Filter $filter)
    {
        if (!self::$instance)
        {
            self::$instance = new self ($filter);
        }

        return self::$instance;
    }

    /**
     * Validates a single form element.
     *
     * @param boolean $mandatory
     * @param string  $name        INPUT name
     * @param string  $filter_rule rule used in Filter
     * @param array   $parameters  Array with parameters, defaults to an empty array()
     *
     * @return boolean
     */
    public function validateSingleElement($mandatory, $name, $filter_rule, $parameters = array())
    {
        if (method_exists($this->filter, $filter_rule) === false)
        {
            throw new Exception_Form("The method $filter_rule is not present in Filter");
        }

        if (!$mandatory && (!$this->filter->isSent($name) || $this->filter->isEmpty($name)))
        {
            return true;
        }

        if ($mandatory && (!$this->filter->isSent($name) || $this->filter->isEmpty($name)))
        {
            return false;
        }

        $total_params = array($name);
        if (!empty($parameters) && is_array($parameters))
        {
            $total_params = array_merge($total_params, array_values($parameters));
        }

        $filter_result = call_user_func_array(array($this->filter, $filter_rule), $total_params);

        // The filter might sanitize the given string, so must be returned cleaned up. Apply filter:
        if (!is_bool($filter_result) || $filter_rule === 'getBoolean')
        {
            $this->fields[$name] = $filter_result;
        }

        return !(false === $filter_result);
    }

    /**
     * Validate an array with several INPUT names that need the same filter rule.
     *
     * @param array  $names
     * @param string $filter_rule
     * @param array  $parameters
     */
    public function validateEqualElements(Array $names, $filter_rule, $parameters = array())
    {
        $res = true;
        foreach ($names as $name)
        {
            $res = $res && $this->validateSingleElement(true, $name, $filter_rule, $parameters);
        }
    }

    /**
     * Returns an array with all the errors found.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Returns an array with all the requirements of the to-be processed form.
     *
     * @return array
     */
    public function getRequirements($form_config)
    {
        $form_elements = Config::getInstance()->getConfig($form_config);
        $requirements  = array();

        foreach ($form_elements as $element)
        {
            $requirements[$element['name']] = $element['required'];
        }

        return $requirements;
    }

    /**
     * Validates a series of form elements. See header of Form.php file for usage.
     *
     * @param string $form_config
     */
    public function validateElements($form_config)
    {
        $form_elements = Config::getInstance()->getConfig($form_config);

        foreach ($form_elements as $key => $element)
        {
            if (!isset($element['name']) || !isset($element['filter']))
            {
                throw new Exception_Form('A form element was passed without the minimum required definition parameters. Element was: ' . var_export($element, true));
            }

            $single_element_validation = $this->validateSingleElement(
                (isset($element['required']) && $element['required']), // Boolean: Field is required?
                $element['name'], // Name of the input
                $element['filter'], // Filter rule to apply
                (isset($element['params']) ? $element['params'] : array()) // Passed params
            );

            if (!$single_element_validation)
            {
                if (isset($element['error']))
                {
                    $this->errors[$element['name']] = $element['error'];
                }
                $this->is_valid = false;
            }
        }

        return $this->is_valid;
    }


    /**
     * Returns true only if ALL added fields are valid.
     *
     * TODO: Support getBoolean with false values.
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->is_valid;
    }

    /**
     * Adds an array of fields to the form.
     *
     * This is useful to maintain all the form field => values within a form object.
     *
     * @param array $fields
     */
    public function addFields(array $fields)
    {
        $this->fields = array_merge($this->fields, $fields);
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getField($key)
    {
        if (isset($this->fields[$key]))
        {
            return $this->fields[$key];
        }

        return false;
    }

    /**
     * Returns a security string that encodes a timestamp in the future.
     *
     * @param  <type> $time
     *
     * @return <type>
     */
    public function getTimeHash($time = 5)
    {
        return Crypt::encrypt(strtotime("+$time seconds")); // Put hash N seconds in the future.
    }

    /**
     * Validates the elements ensuring that the form has been on screen enough seconds.
     *
     * @param string $form_config Configuration file with form definition.
     * @param string $input_name  Optional input name that contains the security hash.
     *
     * @return boolean
     */
    public function isValidTimeHash($input_name)
    {
        if (!$this->filter->isSent($input_name) || $this->filter->isEmpty($input_name))
        {
            $this->errors[$input_name] = 'Security hash not sent'; // You shouldn't display this error. Hackers don't need info.
            return false;
        }
        else
        {
            $time_printed = intval(Crypt::decrypt($this->filter->getString($input_name)));

            if (time() < $time_printed)
            {
                // Too fast cowboy!
                $this->errors[$input_name] = 'Form submitted too fast, might be a BOT';

                return false;
            }
        }

        return true;
    }


}

class Exception_Form extends \Exception
{

}
