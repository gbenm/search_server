<?php

namespace App\Shared\Infrastructure;

use Exception;
use mysqli;

class Database
{
    private $instance = null;

    public function __construct()
    {
        $this->instance = $this->getConnection();
    }

    private function getConnection()
    {
        $host = Env::getDbHost();
        $port = Env::getDbPort();

        $database = Env::getDbName();
        $user = Env::getDbUser();
        $password = Env::getDbPassword();

        $connection = new mysqli(
            hostname: $host,
            port: $port,
            username: $user,
            password: $password,
            database: $database,
        );

        if (mysqli_connect_errno()) {
            throw new Exception("Could not connect to database.");
        }

        return $connection;
    }

    public function select(string $statement, array $params = []): array
    {
        $stmt = $this->executeStatement($statement, $params);
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $result;
    }

    public function insert(string $statement, array $params = []): void
    {
        $stmt = $this->executeStatement($statement, $params);
        $stmt->close();
    }

    public function executeStatement(string $statement, array $params = []): \mysqli_stmt
    {
        $stmt = $this->instance->prepare($statement);
        $stmt->bind_param(...$params);
        $stmt->execute();

        if (!$stmt) {
            throw new Exception("Could not execute statement.");
        }

        return $stmt;
    }
}
