# [Start](../index.md) > [Conventions](./index.md) > PhpDoc

## Inline/incode doc blocks

* single inline doc blocks for data types (e.g. at ServiceManager) without summary and description.
<br/>Example:
```php
/** @var PingService $pingService */
$pingService = Oforge()->Services()->get('ping');
```



## Functions

* Standard getters & setters do not need a summary or description.



## Parameters

* expected data type first, fallback like null last. e.g. <code>string|null</code><br />
