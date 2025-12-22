<?php
namespace App\Database;

use Dotenv\Dotenv;

class Connect
{
    public $connection;
    private $database;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        try {
            $this->database = DatabaseFactory::createConnection($_ENV);
            $this->connection = $this->database->connect();
        } catch (\Exception $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function __destruct()
    {
        if ($this->database) {
            $this->database->close();
        }
    }
}