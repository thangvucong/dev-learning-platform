function query(selector, scope) {
    var root = scope || document;
    return root.querySelector(selector);
}

module.exports = {
    query,
};
