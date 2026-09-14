# Config Manager Library

A lightweight, standalone PHP configuration management library featuring dot-notation syntax, ArrayAccess support, multi-file directory loading, and database persistence (PDO / Custom Drivers).

## Features
- **Dot notation support**: Access deeply nested values using `$config->get('database.connections.mysql.host')`.
- **ArrayAccess Interface**: Treat configuration object like an array `$config['app.name']`.
- **Directory Auto-loader**: Automatically load all `.php` configuration files in a directory into key-partitioned arrays.
- **Database Storage & Persistence**: Load and save configuration directly to a database table via PDO driver (`PdoDatabaseDriver`) or custom drivers.
- **Zero External Runtime Dependencies**: Self-contained with zero external dependencies, easily portable across projects or published as a standalone composer package.

## Package Structure (Composerable)

```text
ttpryg/config-manager/
├── composer.json
├── README.md
├── src/
│   ├── ConfigInterface.php
│   ├── ConfigRepository.php
│   ├── ConfigManager.php
│   └── Drivers/
│       ├── DatabaseDriverInterface.php
│       ├── PdoDatabaseDriver.php
│       └── CallbackDatabaseDriver.php
└── tests/
    └── ConfigRepositoryTest.php
```

## How to Reuse in Another Repository

1. **Option A: Copy Folder Directly**
   Copy the `ttpryg/config-manager/` folder into your new project and add the PSR-4 namespace to your project's `composer.json`:
   ```json
   "autoload": {
       "psr-4": {
           "Ttpryg\\Config\\": "ttpryg/config-manager/src/"
       }
   }
   ```

2. **Option B: Path Repository (Local Composer Package)**
   Add as a local path repository in another project's `composer.json`:
   ```json
   "repositories": [
        {
            "type": "path",
            "url": "./ttpryg/config-manager"
        }
    ],
   "require": {
       "ttpryg/config-manager": "*"
   }
   ```

3. **Option C: Publish to Git / Packagist**
   Push the `ttpryg/config-manager` directory to its own GitHub repository (e.g. `github.com/your-username/config-manager`) and require it via standard composer:
   ```bash
   composer require ttpryg/config-manager
   ```

## Quick Start Example

```php
use Ttpryg\Config\ConfigManager;
use Ttpryg\Config\ConfigRepository;
use Ttpryg\Config\Drivers\PdoDatabaseDriver;

// Method 1: Create manually
$config = new ConfigRepository([
    'app' => [
        'name' => 'My App',
        'env' => 'production',
    ],
]);

// Access via dot-notation
$appName = $config->get('app.name'); // "My App"
$debug = $config->get('app.debug', false); // false (default)

// Set nested values
$config->set('database.mysql.host', '127.0.0.1');

// ArrayAccess syntax
$config['jwt.secret'] = 'secret-key';
$secret = $config['jwt.secret'];

// Method 2: Load from directory containing config PHP files
$config = ConfigManager::createFromDirectory(__DIR__ . '/config');

// Method 3: Database Storage (PDO Driver)
$pdo = new PDO('mysql:host=localhost;dbname=slim_db', 'root', 'password');
$dbDriver = new PdoDatabaseDriver($pdo, 'configs');

// Save config to database
$config->saveToDatabase($dbDriver);

// Load config from database
$dbConfig = ConfigManager::createFromDatabase($dbDriver);
$siteName = $dbConfig->get('site.name');
```
