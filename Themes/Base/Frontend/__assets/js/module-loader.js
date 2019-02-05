/**
 * ES5 based namespace.
 * The Oforge namespace is used to register javascript modules from other files.
 * Other modules will be loaded when the selector matches. Otherwise it will be ignored
 */
var Oforge = (function() {

    return {
        /**
         * Stores the registered modules in an array for later use
         */
        registeredModules: [],

        /**
         * Register a new module
         * @param selector The module name is an element selector. Our recommendation is to use data-attributes like data-module="modulename"
         * @param moduleData An object literal that has to consist of two properties: name (the name of the module for internal use),
         *                   init() (function to initialize the module)
         */
        register: function (selector, moduleData) {
            var self = this;
            var moduleElements = document.querySelectorAll(selector);

            if ( self.registeredModules.some(function (element) {
                if (element.hasOwnProperty.call(element, 'name')) {
                    return element.name === selector;
                }
            })) {
                console.log(selector +  " already registered");
                return;
            }

            if (moduleElements.length < 1) {
                return null;
            }

            if (
                moduleData.hasOwnProperty('name') &&
                moduleData.hasOwnProperty('init') &&
                typeof moduleData.init === 'function'
            ) {
                self.registeredModules.push(moduleData);
            }
        }
    };
})();

/**
 * After all Dom content is loaded, initialize all registered modules.
 */
document.addEventListener('DOMContentLoaded', function (event) {

    if (typeof Oforge !== 'undefined') {
        Oforge.registeredModules.forEach(function (elem, index) {
            elem.init();
        });
    }
});
