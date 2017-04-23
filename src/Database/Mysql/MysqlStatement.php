<?php
/**
 * Created by PhpStorm.
 * User: obokaman
 * Date: 22/4/17
 * Time: 13:50
 */

namespace Sifo\Database\Mysql;

use PDO;
use PDOStatement;
use Sifo\Http\Domains;

/**
 * DbStatement class that is extended to customize some PDO functionality.
 */
class MysqlStatement extends PDOStatement
{
    /**
     * The pdo object instance.
     *
     * @var PDO Object.
     */
    public $dbh;

    /**
     * The domains.config params related to the database.
     *
     * @var array
     */
    protected $db_params;

    /**
     * Construction method. Sets the pdo object and the db parameters.
     *
     * @param PDO $dbh The pdo instance executing the statement.
     * @param string $profile The profile being used for this statement.
     */
    protected function __construct($dbh, $profile)
    {
        $this->dbh = $dbh;
        $params = Domains::getInstance()->getDatabaseParams();

        if (!array_key_exists($profile, $params)) {
            $params[$profile] = $params;
        }

        $this->db_params = $params[$profile];
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
        return parent::execute($parameters);
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
        if ($fetch_argument === null) {
            return parent::fetchAll($fetch_style);
        }

        return parent::fetchAll($fetch_style, $fetch_argument, $ctor_args);
    }
}
