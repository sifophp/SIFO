<?php
/**
 * Created by PhpStorm.
 * User: obokaman
 * Date: 22/4/17
 * Time: 13:16
 */

namespace Sifo\Debug;

use PDO;
use Sifo\Benchmark;
use Sifo\Database\Mysql\MysqlStatement;

/**
 * DbDebugStatement class that is extended for debugging purposes.
 */
class DebugMysqlStatement extends MysqlStatement
{
    /**
     * Binded parameters ( not binded values ).
     *
     * @var array
     */
    protected $binded_params = array();

    /**
     * The fetched result.
     *
     * Used to avoid the resultset of returning an empty array in the actual call
     * as the debug is the first that fetches the result, making that the database
     * cursor scrolls to the end.
     *
     * @var array
     */
    protected $result = null;

    /**
     * Binds a parameter to be replaced in the query string.
     *
     * @param mixed $parameter The parameter to bind.
     * @param mixed &$variable The variable to use in the binding.
     * @param int $data_type The optional PDO data type to bind.
     * @param int $length The binded value length.
     * @param mixed $driver_options The optional driver options.
     *
     * @return boolean True on success, false on failure.
     */
    public function bindParam($parameter, &$variable, $data_type = \PDO::PARAM_STR, $length = 0, $driver_options = null)
    {
        $formatted_value = ($data_type == \PDO::PARAM_STR ? '"' . $variable . '"' : $variable);
        $this->binded_params[$parameter] = $formatted_value;

        return parent::bindParam($parameter, $variable, $data_type, $length, $driver_options);
    }

    /**
     * Executes the current statement.
     *
     * @param array $parameters The array of parameters to be replaced in the statement.
     *
     * @return bool True if everything went OK, false otherwise.
     */
    public function execute($parameters = null)
    {
        Benchmark::getInstance()->timingStart('db_queries');

        $result = parent::execute($parameters);

        $query_time = Benchmark::getInstance()->timingCurrentToRegistry('db_queries');

        $query_string = $this->getPopulatedQuery();
        $query_string = $this->_replacePreparedParameters($query_string, $parameters);

        preg_match('/\/\* (.*?) \*\/\n(.*)/s', $query_string, $matches);
        $context = $matches[1];
        $query_string = $matches[2];

        Mysql::setDebug($query_string, $query_time, $context, $this, $this->db_params);

        if (!$result) {
            trigger_error("Database error: " . implode(' ', $this->errorInfo()), E_USER_WARNING);
        }

        return $result;
    }

    protected function getPopulatedQuery()
    {
        $query_string = $this->queryString;

        foreach ($this->binded_params as $param => $value) {
            $query_string = str_replace($param, $value, $query_string);
        }

        return $query_string;
    }

    private function _replacePreparedParameters($query_string, $parameters)
    {
        if (!$parameters) {
            return $query_string;
        }

        foreach ($parameters as $param => $value) {
            if (!is_numeric($value)) {
                $value = '"' . $value . '"';
            }
            $query_string = str_replace($param, $value, $query_string);
        }

        return $query_string;
    }

    /**
     * Fetches the resultset. Extended to make PDO::FETCH_ASSOC as default $fetch_style.
     *
     * @param integer $fetch_style Controls how the next row will be returned to the caller. This value must be one of the PDO::FETCH_* constants, defaulting to
     *                                    PDO::FETCH_ASSOC.
     * @param integer $cursor_orientation For a PDOStatement object representing a scrollable cursor, this value determines which row will be returned to the caller.
     *                                    This value must be one of the PDO::FETCH_ORI_* constants, defaulting to PDO::FETCH_ORI_NEXT. To request a scrollable cursor for
     *                                    your PDOStatement object, you must set the PDO::ATTR_CURSOR attribute to PDO::CURSOR_SCROLL when you prepare the SQL statement
     *                                    with PDO::prepare().
     * @param integer $cursor_offset For a PDOStatement object representing a scrollable cursor for which the cursor_orientation parameter is set to
     *                                    PDO::FETCH_ORI_ABS, this value specifies the absolute number of the row in the result set that shall be fetched.
     *
     * @return mixed
     */
    public function fetch(
        $fetch_style = PDO::FETCH_ASSOC,
        $cursor_orientation = PDO::FETCH_ORI_NEXT,
        $cursor_offset = 0
    ) {
        if ($fetch_style !== PDO::FETCH_ASSOC) {
            trigger_error('Debug still doesn\'t support a fetch stype different from PDO::FETCH_ASSOC!',
                E_USER_WARNING);
        }

        if ($this->result !== null) {
            return array_shift($this->result);
        }

        return parent::fetch($fetch_style, $cursor_orientation, $cursor_offset);
    }

    /**
     * Returns an array containing all of the result set rows.
     *
     * @param integer $fetch_style Controls the contents of the returned array as documented in PDOStatement::fetch().
     * @param mixed $fetch_argument This argument have a different meaning depending on the value of the fetch_style parameter.
     * @param array $ctor_args Arguments of custom class constructor when the fetch_style parameter is PDO::FETCH_CLASS.
     *
     * @return array
     */
    public function fetchAll($fetch_style = PDO::FETCH_ASSOC, $fetch_argument = null, $ctor_args = array())
    {
        if ($this->result !== null) {
            return $this->result;
        }

        if ($fetch_argument === null) {
            return $this->result = parent::fetchAll($fetch_style);
        }

        return $this->result = parent::fetchAll($fetch_style, $fetch_argument, $ctor_args);
    }
}
