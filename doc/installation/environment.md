[back](../index.md)

# Development environment

##### Table of Contents  
* [Requirements](#requirements)  
* [PHPStorm](#phpstorm)
  * [Configuration](#configuration)
  * [Helper tools](#helper-tools)

## Requirements

* **required**
  * composer (latest)
  * git(latest)
  * php must be in system path


* **recommended**
  * PHPStorm >= 2018.3
    * Plugins:
      * .gitignore
      * php-toolbox
  * XAMPP >= 7.2


## PHPStorm
JetBrains PhpStorm is a commercial, cross-platform IDE (integrated development environment) for PHP built on JetBrains' IntelliJ IDEA platform.

### Configuration
* The project provides code style settings
* Set project php version to 7.2 (file > settings > language & frameworks > php)

### Helper tools


#### PhpStorm advanced metadata

https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html

Example:
```
namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Modules\Core\Manager\Services\ServiceManager::get(0), map([
            'ping'           => \Oforge\Engine\Modules\Core\Services\PingService::class,
        ]));
    }

}
```
#### Php-Toolbox

Example:
```
{
    "registrar": [
        {
            "signatures": [
                {
                    "class": "\\Oforge\\Engine\\Modules\\Cronjob\\Services\\CronjobService",
                    "method": "addCronjob",
                    "type": "array_key",
                    "index": 0
                }
            ],
            "provider": "oforge.service.cronjob.addCronjob",
            "language": "php"
        }
    ],
    "providers": [
        {
            "name": "oforge.service.cronjob.addCronjob",
            "defaults": {
                "icon": "com.jetbrains.php.PhpIcons.CONSTANT",
                "type": "string"
            },
            "items": [
                {
                    "lookup_string": "type",
                    "type_text": "string",
                    "tail_text": " of [CommandCronjob::class | CustomCronjob::class]"
                },
                {
                    "lookup_string": "name",
                    "type_text": "string",
                    "tail_text": " [required]"
                },
                ...
            ]
        }
    ]
}

```
