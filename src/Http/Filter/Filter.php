<?php

namespace Sifo\Http\Filter;

/**
 * Filters the request array checking that the values accomplish the given filters.
 *
 * It DOES NOT modify the original value. Use SANITIZE filters for that purpose.
 *
 * @see http://php.net/manual/en/filter.filters.validate.php
 */
class Filter
{
    /**
     * Regular expression for email validation.
     * If you want to know why we're not using the filter_var method with the FILTER_VALIDATE_EMAIL flag, see:
     * https://groups.google.com/forum/?hl=en#!topic/sifophp/5o0tkI2nC44
     */
    const VALID_EMAIL_REGEXP = '/^(([a-z0-9_%\-]+\.?)+)?(\+(([a-z0-9_%\-]+\.?)|)+)?[a-z0-9\-_]@(([a-z0-9\-]+)?[a-z0-9]\.)+([a-z]{2}|com|edu|org|net|biz|info|name|aero|biz|info|jobs|travel|museum|name|cat|asia|coop|jobs|mobi|tel|pro|arpa|gov|mil|int|post|xxx)$/i';

    /** @var self */
    static protected $instance;

    /** @var array */
    protected $request;

    protected function __construct(array $request)
    {
        $this->request = &$request;
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self ($_POST);
        }

        return self::$instance;
    }

    public function setVar(string $key, $value)
    {
        $this->request[$key] = $value;
    }

    public function isSent(string $var_name): bool
    {
        return isset($this->request[$var_name]);
    }

    public function sentVars(): array
    {
        return array_keys($this->request);
    }

    public function isEmpty(string $var_name): bool
    {
        // I changed empty by strlen because we was sending that 0 is an empty field and this is a correct integer. Minutes for example:
        return (!isset($this->request[$var_name]) || (is_array($this->request[$var_name]) && (count($this->request[$var_name]) == 0)) || (!is_array($this->request[$var_name]) && (strlen($this->request[$var_name]) == 0)));
    }

    public function countVars(): int
    {
        return count($this->request);
    }

    /**
     * Returns a string using the FILTER_DEFAULT.
     *
     * @param string $var_name
     * @param bool $sanitized
     *
     * @return bool|string
     */
    public function getString(string $var_name, bool $sanitized = false)
    {
        if (!isset($this->request[$var_name])) {
            return false;
        }

        if (false === $sanitized) {
            return filter_var($this->request[$var_name], FILTER_DEFAULT);
        } else {
            // Used the flag encode LOW because allows Chinese Characters (encode HIGH don't): 地 图
            return filter_var($this->request[$var_name], FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
        }
    }

    /**
     * Get a variable without any type of filtering.
     *
     * @param string $var_name
     *
     * @return mixed
     */
    public function getUnfiltered(string $var_name)
    {
        if (!isset($this->request[$var_name])) {
            return false;
        }

        return $this->request[$var_name];
    }

    /**
     * Returns an email if filtered or false if it is not valid.
     *
     * @param string $var_name Request containing the variable.
     * @param boolean $check_dns Check if domain passed has a valid MX record.
     *
     * @return string|bool
     */
    public function getEmail(string $var_name, bool $check_dns = false)
    {
        if (!isset($this->request[$var_name])) {
            return false;
        }

        if (preg_match(self::VALID_EMAIL_REGEXP, $this->request[$var_name])) {
            if ($check_dns) {
                $exploded_email = explode('@', $this->request[$var_name]);

                return (checkdnsrr($exploded_email[1], 'MX') ? $this->request[$var_name] : false);
            } else {
                return $this->request[$var_name];
            }
        }

        return false;
    }

    /**
     * Returns if a value might be considered as boolean (1, true, on, yes)
     *
     * @param string $var_name
     *
     * @return bool
     */
    public function getBoolean(string $var_name): bool
    {
        if (!isset($this->request[$var_name])) {
            return false;
        }

        return filter_var($this->request[$var_name], FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Returns a float value for the given var.
     *
     * @param string $var_name
     * @param boolean $decimal
     *
     * @return float|bool
     */
    public function getFloat(string $var_name, bool $decimal = null)
    {
        if (!isset($this->request[$var_name])) {
            return false;
        }

        if (isset($decimal)) {
            $decimal = array('options' => array('decimal' => $decimal));
        }

        return filter_var($this->request[$var_name], FILTER_VALIDATE_FLOAT, $decimal);
    }

    /**
     * Returns the integer value of the var or false.
     *
     * @param string $var_name
     * @param int $min_range
     * @param int $max_range
     *
     * @return int|bool
     */
    public function getInteger(string $var_name, int $min_range = null, int $max_range = null)
    {
        if (!isset($this->request[$var_name])) {
            return false;
        }

        $options = null;

        if (isset($min_range)) {
            $options['options']['min_range'] = $min_range;
        }

        if (isset($max_range)) {
            $options['options']['max_range'] = $max_range;
        }

        return filter_var($this->request[$var_name], FILTER_VALIDATE_INT, $options);
    }

    /**
     * Returns the IP value of the var or false.
     *
     * @param string $var_name Name of the variable
     * @param string $min_range Minimum value accepted
     * @param string $max_range Maximum value accepted
     *
     * @return mixed
     */
    public function getIP(string $var_name, $min_range = null, $max_range = null)
    {
        if (!isset($this->request[$var_name])) {
            return false;
        }

        // Allow IPv4 Ips.
        $options['flags'] = FILTER_FLAG_IPV4;

        if (isset($min_range)) {
            $options['options']['min_range'] = $min_range;
        }

        if (isset($max_range)) {
            $options['options']['max_range'] = $max_range;
        }

        return filter_var($this->request[$var_name], FILTER_VALIDATE_IP, $options);
    }

    public function getRegexp(string $var_name, string $regexp)
    {
        if (!isset($this->request[$var_name])) {
            return false;
        }

        return filter_var($this->request[$var_name], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $regexp]]);
    }

    public function getUrl(string $var_name)
    {
        if (!isset($this->request[$var_name])) {
            return false;
        }

        $options['options']['flags'] = FILTER_FLAG_PATH_REQUIRED;

        return filter_var($this->request[$var_name], FILTER_VALIDATE_URL, $options);
    }

    public function getInArray(string $var_name, array $list_of_elements)
    {
        if (!isset($this->request[$var_name])) {
            return false;
        }

        if (!in_array($this->request[$var_name], $list_of_elements, true)) {
            return false;
        }

        return $this->request[$var_name];
    }

    /**
     * Return an array like getArray but, in this case, the array is a serialized one.
     * Used to send arrays from javascript.
     *
     * @param string $var_name
     * @param string $filter_function Is the function to use with each array field.
     *
     * @return array|bool
     */
    public function getArrayFromSerialized(string $var_name, string $filter_function = null)
    {
        if (!isset($this->request[$var_name])) {
            return false;
        }
        parse_str($this->request[$var_name], $this->request[$var_name]);

        return $this->getArray($var_name, $filter_function);
    }

    /**
     * Returns an array on the post UNFILTERED.
     *
     * @param string $var_name
     * @param string $filter_function
     *
     * @return array|bool
     */
    public function getArray(string $var_name, string $filter_function = null)
    {

        if (!isset($this->request[$var_name]) || !is_array($this->request[$var_name])) {
            return false;
        }

        // Returns an unfiltered Array
        if (null === $filter_function) {
            return $this->request[$var_name];
        }

        trigger_error('The function Filter::getArray is not implemented yet so you are not filtering anything.');
    }

    /**
     * Checks if a string is a valid.
     *
     * Matches:
     * 1/1/2005 | 29/02/12 | 29/02/2400
     * Non-Matches:
     * 29/2/2005 | 29/02/13 | 29/02/2200
     *
     * @param string $var_name
     * @param string $format Any format accepted by date()
     *
     * @return string|bool String of the date or false.
     */
    public function getDate(string $var_name, string $format = 'd-m-Y')
    {
        if (!isset($this->request[$var_name])) {
            return false;
        }

        $date = \DateTime::createFromFormat($format, $this->request[$var_name]);
        if (empty($date)) {
            return false;
        }

        return $date->format($format);
    }

    public function getDateWithDefaultValue(
        string $var_name,
        \DateTimeInterface $default_date,
        string $format = 'd-m-Y'
    ) {
        $date = $this->getDate($var_name, $format);
        if (empty($date)) {
            return $default_date;
        }

        return $date;
    }

    public function getDateMultiValue(
        string $var_name,
        int $minimum_years = null,
        string $second_var_name = null,
        string $third_var_name = null,
        string $format = 'd-m-Y'
    ) {
        if (!isset($this->request[$var_name])) {
            return false;
        }

        $field_values = $this->request[$var_name];
        if (null !== $second_var_name && null !== $third_var_name) {
            if (isset($this->request[$second_var_name]) && isset($this->request[$third_var_name])) {
                $field_values = $this->request[$var_name] . '/' . $this->request[$second_var_name] . '/' . $this->request[$third_var_name];
            }
        }

        $date = \DateTime::createFromFormat($format, $field_values);
        if ($date !== false) {
            if (null !== $minimum_years) {
                if (new \DateTime('now') < $date->add(new \DateInterval("P{$minimum_years}Y"))) {
                    return false;
                }
            }

            return $date->format($format);
        }

        return false;
    }

    public function getRawRequest(): array
    {
        return $this->request;
    }
}
