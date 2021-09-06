Akismet.panel.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'akismet-panel-home',
        baseCls: 'modx-formpanel',
        cls: "container",
        items: [{
            html: '<h2>' + _('akismet') + '</h2>',
            defaults: { border: false, autoHeight: true },
            border: true,
            cls: 'modx-page-header'
        },{
            xtype: 'modx-panel',
            defaults: { border: false, autoHeight: true },
            border: true,
            items: [{
                defaults: { autoHeight: true },
                items: [{
                    html: '<p>' + _('akismet.description') + '</p>',
                    cls: 'akismet-home-panel',
                    bodyCssClass: 'panel-desc',
                },{
                    xtype: 'akismet-grid-forms'
                    ,cls: 'main-wrapper'
                    ,preventRender: true
                }],
            }],
            listeners: {
                afterrender: function (tabPanel) {
                    tabPanel.doLayout();
                },
            },
        }],
    });
    Akismet.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(Akismet.panel.Home, MODx.Panel);
Ext.reg('akismet-panel-home', Akismet.panel.Home);