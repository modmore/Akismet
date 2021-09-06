var Akismet = function (config) {
    config = config || {};
    Akismet.superclass.constructor.call(this, config);
};
Ext.extend(Akismet, Ext.Component, {
    page: {},
    window: {},
    grid: {},
    tree: {},
    panel: {},
    combo: {},
    field: {},
    config: {},
});
Ext.reg('akismet', Akismet);
Akismet = new Akismet();