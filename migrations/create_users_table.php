<?php
// migrations/create_users_table.php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Database\DatabaseFactory;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

try {
    // Create database connection
    $database = DatabaseFactory::createConnection($_ENV);
    $connection = $database->connect();

    // Create users table based on database type
    $dbType = $_ENV['DB_TYPE'] ?? 'sqlite';

    if ($dbType === 'sqlite') {
        // For SQLite, we need to ensure the database file exists
        $dbPath = $_ENV['DB_PATH'] ?? __DIR__ . '/../database.sqlite';
        if (!file_exists($dbPath)) {
            touch($dbPath);
        }

        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                avatar TEXT,
                provider TEXT,
                provider_id TEXT,
                email_verified_at DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
    } elseif ($dbType === 'mysql') {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                avatar VARCHAR(500),
                provider VARCHAR(50),
                provider_id VARCHAR(255),
                email_verified_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            );
        ";
    } elseif ($dbType === 'postgresql') {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                avatar VARCHAR(500),
                provider VARCHAR(50),
                provider_id VARCHAR(255),
                email_verified_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ";
    } else {
        throw new Exception("Unsupported database type: " . $dbType);
    }

    // Execute the query
    $connection->exec($sql);

    echo "Users table created successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}