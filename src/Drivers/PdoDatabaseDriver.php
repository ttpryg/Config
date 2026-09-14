<?php

declare(strict_types=1);

namespace Ttpryg\Config\Drivers;

use PDO;

class PdoDatabaseDriver implements DatabaseDriverInterface
{
    public function __construct(
        protected PDO $pdo,
        protected string $table = 'configs',
        protected string $keyColumn = 'key',
        protected string $valueColumn = 'value'
    ) {
        $this->ensureTableExists();
    }

    public function get(string $key): ?string
    {
        $stmt = $this->pdo->prepare("SELECT {$this->valueColumn} FROM {$this->table} WHERE {$this->keyColumn} = :key LIMIT 1");
        $stmt->execute(['key' => $key]);

        $result = $stmt->fetchColumn();

        return $result !== false ? (string) $result : null;
    }

    public function set(string $key, string $value): void
    {
        $existing = $this->get($key);

        if ($existing !== null) {
            $stmt = $this->pdo->prepare("UPDATE {$this->table} SET {$this->valueColumn} = :value WHERE {$this->keyColumn} = :key");
            $stmt->execute(['value' => $value, 'key' => $key]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO {$this->table} ({$this->keyColumn}, {$this->valueColumn}) VALUES (:key, :value)");
            $stmt->execute(['key' => $key, 'value' => $value]);
        }
    }

    public function forget(string $key): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$this->keyColumn} = :key");
        $stmt->execute(['key' => $key]);
    }

    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT {$this->keyColumn}, {$this->valueColumn} FROM {$this->table}");
        if (! $stmt) {
            return [];
        }

        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        return is_array($rows) ? $rows : [];
    }

    protected function ensureTableExists(): void
    {
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'sqlite') {
            $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
                {$this->keyColumn} VARCHAR(255) PRIMARY KEY,
                {$this->valueColumn} TEXT
            )";
        } else {
            $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
                {$this->keyColumn} VARCHAR(255) PRIMARY KEY,
                {$this->valueColumn} LONGTEXT
            )";
        }

        $this->pdo->exec($sql);
    }
}
