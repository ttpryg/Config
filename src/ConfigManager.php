<?php

declare(strict_types=1);

namespace Ttpryg\Config;

use Ttpryg\Config\Drivers\DatabaseDriverInterface;

class ConfigManager
{
    /**
     * Create a ConfigRepository instance pre-loaded with files from a directory.
     */
    public static function createFromDirectory(string $directoryPath): ConfigRepository
    {
        $repository = new ConfigRepository;
        $repository->loadDir($directoryPath);

        return $repository;
    }

    /**
     * Create a ConfigRepository instance pre-loaded from a database driver.
     */
    public static function createFromDatabase(DatabaseDriverInterface $driver): ConfigRepository
    {
        $repository = new ConfigRepository;
        $repository->loadDatabase($driver);

        return $repository;
    }

    /**
     * Create a ConfigRepository instance from an array of configuration values.
     */
    public static function createFromArray(array $items): ConfigRepository
    {
        return new ConfigRepository($items);
    }
}
