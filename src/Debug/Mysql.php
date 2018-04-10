<?php

namespace Sifo\Debug;

use Sifo\Benchmark;
use Sifo\Database\Mysql\Mysql as OriginalMysql;
use Sifo\Http\Domains;

/**
 * Database debug class. Extends the parent with benchmarking and debug utilities.
 *
 * This is done in a separate class to avoid decreased performance in production environments.
 */
class Mysql extends OriginalMysql
{
    /** @var self */
    static private $instance;

    /** @var array */
    static private $executed_queries = [];

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

            self::$instance[$profile] = new self($profile);

            Benchmark::getInstance()->timingCurrentToRegistry('db_connections');
        }

        return self::$instance[$profile];
    }

    protected static function getStatementClass()
    {
        return DebugMysqlStatement::class;
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
        preg_match('/\/\* (.+)? \*\/\n(.*)/s', $statement, $matches);
        $context = $matches[1];
        $statement = $matches[2];

        Benchmark::getInstance()->timingStart('db_queries');

        $result = $this->pdo->query($statement);

        $query_time = Benchmark::getInstance()->timingCurrentToRegistry('db_queries');

        static::setDebug($statement, $query_time, $context, $result, $this->db_params, $this->pdo);

        return $result;
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
        Benchmark::getInstance()->timingStart('db_' . $method);

        $result = call_user_func_array(array($this->pdo, $method), $arguments);

        $query_time = Benchmark::getInstance()->timingCurrentToRegistry('db_' . $method);

        if ($arguments !== array()) {
            static::setDebug($arguments[0], $query_time, $arguments[1], $result, $this->db_params);
        }

        return $result;
    }

    /**
     * Fills some debug data to be displayed in the debug interface.
     *
     * @param string $statement The sql statement being queried.
     * @param float $query_time The time that the query needed to be completed.
     * @param string $context The context of the sql query.
     * @param integer|array|\PDOStatement $resultset The result of the query.
     * @param $db_params
     * @param $pdo
     */
    public static function setDebug($statement, $query_time, $context, $resultset, $db_params, $pdo = null)
    {
        if (false === Domains::getInstance()->getDebugMode()) {
            return;
        }

        if ($resultset !== false) {
            $error = $resultset->errorInfo();
            $resultset_array = $resultset->fetchAll();
            $rows_num = $resultset->rowCount();
        } else {
            $error = $pdo->errorInfo();
            $resultset_array = 0;
            $rows_num = 0;
        }

        $sql = '/* ' . $context . ' */' . PHP_EOL . $statement;
        $debug_query = array(
            "tag" => $context,
            "sql" => $sql,
            "type" => ((0 === stripos($statement, 'SELECT')) ? 'read' : 'write'),
            "host" => $db_params['db_host'],
            "database" => $db_params['db_name'],
            "user" => $db_params['db_user'],
            "trace" => self::generateTrace(debug_backtrace(false)),
            // Show a table with the method name and number (functions: Affected_Rows, Last_InsertID
            "resultset" => $resultset_array,
            "time" => $query_time,
            "error" => (isset($error[2]) !== false) ? $error[2] : false
        );

        $debug_query['rows_num'] = $rows_num;

        if ($debug_query['error'] !== false) {
            $database_data = Domains::getInstance()->getDatabaseParams();
            $path = !empty($database_data['error_log_path']) ? $database_data['error_log_path'] : ROOT_PATH . '/logs/errors_database.log';

            // Log mysql_errors to disk:
            file_put_contents(
                $path,
                "================================\nDate: " . date('d-m-Y H:i:s') . "\nError:\n- SQL State: {$error[0]}\n- Code: {$error[1]}\n- Message: {$error[2]}\n",
                FILE_APPEND
            );
            Debug::push('queries_errors', $error);
        }

        if (in_array($sql, self::$executed_queries)) {
            $debug_query['duplicated'] = true;
            Debug::push('duplicated_queries', 1);
        }

        self::$executed_queries[] = $sql;

        Debug::push('queries', $debug_query);
    }

    /**
     * Generates a trace to know where the query was executed.
     *
     * @return string
     */
    public static function generateTrace($debug_backtrace)
    {
        array_shift($debug_backtrace);

        $trace = '';
        foreach ($debug_backtrace as $key => $step) {
            if (!(isset($step['file']) && isset($step['line']) && isset($step['class']) && isset($step['function']))) {
                $trace .= "#$key {$step['function']}\n";
                continue;
            }

            $trace .= "#$key {$step['file']}({$step['line']}) : {$step['class']}::{$step['function']}()\n";
        }

        return $trace;
    }
}
