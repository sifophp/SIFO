<?php

declare(strict_types=1);

namespace Sifo\Test\Sifo;

use PHPUnit\Framework\TestCase;
use Sifo\DebugMysql;
use Sifo\Mysql;

class DebugMysqlTest extends TestCase
{
    protected function tearDown(): void
    {
        $mysql = DebugMysql::getInstance();
        /** @var \PDOStatement $statement */
        $statement = $mysql->prepare(
            <<<SQL
DROP TABLE projects;
SQL
        );

        $statement->execute();
    }

    public function testFetchAll(): void
    {
        $mysql = DebugMysql::getInstance();

        /** @var \PDOStatement $statement */
        $statement = $mysql->prepare(
            <<<SQL
CREATE TABLE IF NOT EXISTS projects (
    project_id   INTEGER PRIMARY KEY,
    project_name TEXT    NOT NULL
);
SQL
        );

        $statement->execute();
        /** @var \PDOStatement $statement */
        $statement = $mysql->prepare(
            <<<SQL
INSERT INTO projects VALUES (1, 'test'),(2,'test2');
SQL
        );

        $statement->execute();

        /** @var \PDOStatement $statement */
        $statement = $mysql->prepare(
            <<<SQL
SELECT * FROM projects;
SQL
        );
        $statement->execute();

        $result = $statement->fetchAll();

        $this->assertSame([
            [
                "project_id" => "1",
                "project_name" => "test"
            ],
            [
                "project_id" => "2",
                "project_name" => "test2"
            ]
        ], $result);
    }
}
