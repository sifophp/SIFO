<?php

namespace Sifo\Database\Mysql;

use PDO;
use Sifo\Benchmark;
use Sifo\Http\Domains;

/**
 * Database class. Uses PDO.
 */
class Mysql
{
    /** @var static */
    static private $instance = null;

    /**
     * The PDO instance.
     *
     * @var PDO Object.
     */
    protected $pdo;

    /**
     * The domains.config params related to the database.
     *
     * @var array
     */
    protected $db_params;

    /**
     * Initializes the PDO object with the domains.config.php database configuration.
     *
     * @param string $profile The database server to connect to.
     */
    public function __construct($profile)
    {
        $this->db_params = Domains::getInstance()->getDatabaseParams();
        $init_commands = array();

        if (!empty($this->db_params['db_init_commands'])) {
            $init_commands = array(PDO::MYSQL_ATTR_INIT_COMMAND => implode(';', $this->db_params['db_init_commands']));
        }

        $this->pdo = new PDO(
            "mysql:host={$this->db_params['db_host']};dbname={$this->db_params['db_name']}",
            $this->db_params['db_user'], $this->db_params['db_password'], $init_commands

        );

        $this->pdo->setAttribute(PDO::ATTR_STATEMENT_CLASS, [static::getStatementClass(), [$this->pdo, $profile]]);
    }

    protected static function getStatementClass()
    {
        return MysqlStatement::class;
    }

    /**
     * Singleton static method.
     *
     * @param string $profile The database server to connect to.
     *
     * @return Mysql
     */
    public static function getInstance($profile = 'default')
    {
        if (!isset(self::$instance[$profile])) {
            Benchmark::getInstance()->timingStart('db_connections');

            self::$instance[$profile] = new Mysql($profile);

            Benchmark::getInstance()->timingCurrentToRegistry('db_connections');
        }

        return self::$instance[$profile];
    }

    /**
     * Calls the pdo query method.
     *
     * @param string $statement The query statement to be executed in the database server.
     *
     * @return \PDOStatement
     */
    public function query($statement)
    {
        return $this->pdo->query($statement);
    }

    /**
     * Prepares a statement.
     *
     * @param string $statement This must be a valid SQL statement for the target database server.
     * @param array $driver_options This array holds one or more key=>value pairs to set attribute values for the PDOStatement object that this method returns. You
     *                               would most commonly use this to set the PDO::ATTR_CURSOR value to PDO::CURSOR_SCROLL to request a scrollable cursor. Some drivers
     *                               have driver specific options that may be set at prepare-time.
     *
     * @return \PDOStatement
     */
    public function prepare($statement, $driver_options = array())
    {
        return $this->pdo->prepare($statement, $driver_options);
    }

    /**
     * Returns the last inserted id.
     *
     * @return string
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Calls a pdo method.
     *
     * @param string $method A method in the pdo object.
     * @param array $arguments The array of arguments to pass to the method.
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->pdo, $method), $arguments);
    }
}
