Akismet.panel.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'akismet-panel-home',
        border: false,
        baseCls: 'modx-formpanel',
        cls: "container",
        items: [{
            html: '<h2>' + _('akismet') + '</h2>',
            defaults: { border: false, autoHeight: true },
            border: true,
            cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs',
            defaults: { border: false, autoHeight: true },
            border: true,
            items: [{
                title: _('akismet.form_submissions'),
                defaults: { autoHeight: true },
                items: [{
                    html: '<p>' + _('akismet.description') + '</p>',
                    border: false,
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