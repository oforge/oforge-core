// removes cyclic references in javascript objects
// found at mozilla.org
// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Errors/Cyclic_object_value
const getCircularReplacer = () => {
	const seen = new WeakSet;
	return (key, value) => {
		if (typeof value === "object" && value !== null) {
			if (seen.has(value)) {
				return;
			}
			seen.add(value);
		}
		return value;
	};
};

// JSON Stringify function that prevents cyclic object value errors
function jsonStringify(obj, pretty_spacing = 4) {
	return JSON.stringify(obj, getCircularReplacer(), pretty_spacing);
}