[back](../index.md)

# Development console command

## Implementation
1. Create a folder `Commands` in your module / plugin.
2. Create a class that extends one of the abstract command classes
3. The constructor requires the console command as a name and a type.
4. Optional implementation of handle method.
5. Add implemented command class name (Class::class) in Bootstrap commands array property.

## Types of commands
* Default
  * visible in help
  * visible in command list
* Extended (commands for administrators)
  * not visible in help
  * visible in extended command list (-e|--extended).
* Development (commands for development purpose like `clear database`)
  * not visible in help
  * visible in special command list (--dev)
* Cronjob (commands for cronjob purpose)
  * not visible in help
  * visible in special command list (--cronjob)
  * visible in cronjob admin backend view
* Hidden
  * always not visible

## Abstract command classes

Examples in console module.

#### AbstractCommand
Basic command class.

#### AbstractBatchCommand

Run all sub commands of namespace with optional excluding.

Example:
* commands: `example:test1`, `example:test2`, `example:test1`
* Batch command: `example` with excluding `example:test1` will execute `example:test1` and`example:test1`.

#### AbstractGroupCommand
Define which other commands should be executed.
