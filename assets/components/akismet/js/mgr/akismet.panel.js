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
            layout: 'column',
            defaults: { border: false, autoHeight: true },
            items: this.getStats(config)
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
Ext.extend(Akismet.panel.Home, MODx.Panel, {
    getStats: function (config) {
        var stats = [];

        stats.push({
            width: 250,
            cls: "main-wrapper",
            defaults: {autoHeight: true},
            items: [{
                html: "<span class='akismet-home-stat'>" + Akismet.stats.spam + "</span>",
            }, {
                html: "<span class='akismet-home-statlabel'>" + _('akismet.spam_blocked') + "</span>",
            }],
            layout: "anchor"
        });

        stats.push({
            width: 250,
            cls: "main-wrapper",
            defaults: {autoHeight: true},
            items: [{
                html: "<span class='akismet-home-stat'>" + Akismet.stats.ham + "</span>",
            }, {
                html: "<span class='akismet-home-statlabel'>" + _('akismet.real_messages') + "</span>",
            }],
            layout: "anchor"
        });

        stats.push({
            width: 250,
            cls: "main-wrapper",
            defaults: {autoHeight: true},
            items: [{
                html: "<span class='akismet-home-stat'>" + Akismet.stats.spam_rate + "</span>",
            }, {
                html: "<span class='akismet-home-statlabel'>" + _('akismet.spam_percentage') + "</span>",
            }],
            layout: "anchor"
        });

        return stats;
    }
});
Ext.reg('akismet-panel-home', Akismet.panel.Home);