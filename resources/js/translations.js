function trans(key) {
    return window.translations && window.translations[key] !== undefined
        ? window.translations[key]
        : key;
}

console.log("Loaded translations:", window.translations);
window.trans = trans;
