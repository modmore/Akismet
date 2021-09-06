Akismet.panel.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'akismet-panel-home',
        border: false,
        baseCls: 'modx-formpanel',
        cls: 'container',
        layout: 'anchor',
        items: [{
            html: '<h2>' + _('akismet') + '</h2>',
            cls: 'modx-page-header'
        },{
            xtype: 'modx-panel',
            defaults: { border: false, autoHeight: true },
            items: [{
                defaults: { autoHeight: true },
                layout: 'anchor',
                items: [{
                    html: '<p>' + _('akismet.description') + '</p>',
                    cls: 'akismet-home-panel',
                    bodyCssClass: 'panel-desc',
                    items: []
                },{
                    xtype: 'akismet-grid-form-submissions',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    Akismet.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(Akismet.panel.Home, MODx.Panel);
Ext.reg('akismet-panel-home', Akismet.panel.Home);