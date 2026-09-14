<?php

declare(strict_types=1);

namespace Ttpryg\Config\Drivers;

interface DatabaseDriverInterface
{
    /**
     * Get a raw value string from the database for a key.
     */
    public function get(string $key): ?string;

    /**
     * Save or update a key-value pair in the database.
     */
    public function set(string $key, string $value): void;

    /**
     * Delete a key from the database.
     */
    public function forget(string $key): void;

    /**
     * Retrieve all key-value configuration rows from the database.
     *
     * @return array<string, string>
     */
    public function all(): array;
}
