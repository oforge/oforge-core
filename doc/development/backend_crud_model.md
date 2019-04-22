[back](../index.md)

# Backend crud management of model
The model **muss contains id property**.

## ViewController
Create a ViewController class that inherits from **Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController** and register it in Bootstrap.

Now the properties **model** and **modelProperties** of the class have to be overwritten:

In **model** you save the **CRUD model**.

With **modelProperties** you define which properties of the model are displayed or editable in the views.

You can disable individual crud functions through the class property **crudActions**.


## Twig Templates
Create twig templates for the CRUD functions that extends from the respective CRUD templates (for every function enabled in **crudActions**).   
