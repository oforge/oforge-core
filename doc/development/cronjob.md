[back](../index.md)

# Development cronjob

## Implementation
1. Create a folder `Cronjobs` in your module / plugin.
2. Create a class that extends one of the cronjob classes
3. Set data in cronjob. Use setter or fromArray method.
4. CustomCronjob: Create & implement handler class that extends from AbstractCronjobHandler.
5. Add implemented command class name (Class::class) in Bootstrap cronjobs array property.

## Cronjob classes

#### CommandCronjob
A CommandCronjob executes a console command with arguments.

#### CustomCronjob
A CustomCronjob executes the handle method of a class that extends from AbstractCronjobHandler.
