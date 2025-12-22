<?php
namespace App\Database;

class DatabaseFactory
{
    public static function createConnection(array $config): DatabaseInterface
    {
        $type = $config['DB_TYPE'] ?? 'mysql';

        switch (strtolower($type)) {
            case 'mysql':
                return new MySQLConnection(
                    $config['DB_HOST'],
                    $config['DB_USER'],
                    $config['DB_PASS'],
                    $config['DB_NAME'],
                    (int)($config['DB_PORT'] ?? 3306)
                );

            case 'sqlite':
                $path = $config['DB_PATH'] ?? __DIR__ . '/../../db.sqlite';
                return new SQLiteConnection($path);

            case 'pgsql':
            case 'postgresql':
                return new PostgreSQLConnection(
                    $config['DB_HOST'],
                    $config['DB_USER'],
                    $config['DB_PASS'],
                    $config['DB_NAME'],
                    (int)($config['DB_PORT'] ?? 5432)
                );

            default:
                throw new \Exception("Unsupported database type: " . $type);
        }
    }
}