// check if the Oforge namespace exists
if (typeof Oforge !== 'undefined') {
    // if it exists, it should have the register function, so register your module
    // the properties "name", "selector" and "init" are required
    // name: the name of your module
    // selector: the html selector to search for. If it is found, the module can be initialized
    // init: the function to initialize the module. This function gets called automatically from the module-loader.js
    // when the DOMContentLoaded event is triggered.
    Oforge.register({
        name: 'exampleModule',
        selector: '.container',
        otherNotRequiredContent: 'some other content, that we can define and that is not required',
        init: function () {
            var self = this;
            var htmlElement = document.querySelectorAll(self.selector);
            console.log("the module " + self.name + ' was registered and initialized successfully!', htmlElement);
            // ...
            // do more stuff
            // ...
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}
