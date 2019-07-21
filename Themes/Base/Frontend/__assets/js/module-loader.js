/**
 * ES5 based namespace.
 * The Oforge namespace is used to register javascript modules from other files.
 * Other modules will be loaded when the selector matches. Otherwise it will be ignored
 */
var Oforge = (function() {
    var validProperties = [
        {name: 'name', type: 'string'},
        {name: 'selector', type: 'string'},
        {name: 'init', type: 'function'}
    ];

    var isValid = function (moduleData) {
        return validProperties.every(function (property) {
           return (
               moduleData.hasOwnProperty.call(moduleData, property.name) &&
               typeof moduleData[property.name] === property.type
           );
        });
    };

    /**
     * Check if the module has already been registered
     * @param moduleName
     * @returns {boolean}
     */
    var moduleExists = function(moduleName) {
        return Oforge.registeredModules.some(function (element) {
            if (element.hasOwnProperty.call(element, 'name')) {
                return element.name === moduleName;
            }
        });
    };

    /**
     * remove a module from the registered module list
     * @param moduleData
     */
    var removeModule = function (moduleData) {
        var moduleIndex = Oforge.registeredModules.findIndex(function (element, index) {
            if (
                element.hasOwnProperty.call(element, 'name') &&
                element.name === moduleData.name
            ) {
                return index;
            }
        });
        Oforge.registeredModules.splice(moduleIndex, 1);
    };

    return {
        /**
         * Stores the registered modules in an array for later use
         */
        registeredModules: [],

        /**
         * Register a new module
         * @param moduleData An object literal that has to consist of the following properties:
         *                   - name (the name of the module for internal use),
         *                   - selector (a css selector for ui)
         *                   - init() (function to initialize the module)
         */
        register: function (moduleData) {

            var self = this;
            var moduleElements;
            var moduleName;

            if (!isValid(moduleData)) {
                console.warn("Module is not valid. ",
                    "Module: ",
                    moduleData,
                    "Module must consist of the following properties: ",
                    validProperties);
                return;
            }

            moduleName = moduleData.name;

            if (moduleExists(moduleName)) {
                console.warn("Module " + moduleName + " is already registered. ", moduleData);
                return;
            }

            moduleElements = document.querySelectorAll(moduleData.selector);
            moduleData.target = moduleElements;

            // the module's selector is not found in the current page context, so we don't need the module here
            if (moduleElements.length < 1) {
                return;
            }

            self.registeredModules.push(moduleData);
        },
        overwrite: function (moduleData) {
            var self = this;
            removeModule(moduleData);
            self.register(moduleData);
        },
        unregister: function (moduleData) {
            // TODO: maybe we have to remove the module's eventlisteners
            removeModule(moduleData);
        }
    };
})();

/**
 * After all Dom content is loaded, initialize all registered modules.
 */
$(document).ready(function() {
    if (typeof Oforge !== 'undefined') {
        Oforge.registeredModules.forEach(function (elem, index) {
            elem.init();
        });
    }
});
