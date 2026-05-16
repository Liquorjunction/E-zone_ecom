function trans(key) {
    return window.translations && window.translations[key] !== undefined
        ? window.translations[key]
        : key;
}

window.trans = trans;
