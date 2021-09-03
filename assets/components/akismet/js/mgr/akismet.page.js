Ext.onReady(function() {
    MODx.load({
        xtype: 'akismet-page-home',
        renderTo: 'akismet-home'
    });
});

Akismet.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        border: false,
        id : 'akismet-page-wrapper',
        components: [{
            cls: 'container',
            xtype: 'akismet-panel-home'
        }],
        buttons: this.getButtons(config)
    });
    Akismet.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(Akismet.page.Home, MODx.Component,{
    getButtons: function() {
        var buttons = [{
            text: 'Help',//_('help_ex'),
            handler: this.loadHelpPane,
            scope: this,
            id: 'modx-abtn-help'
        }];

        if (!Akismet.config.has_donated) {
            buttons.push(['-', {
                text: 'Donate',//_('akismet.donate'),
                handler: this.donate,
                scope: this
            }]);
        }

        return buttons;
    },

    loadHelpPane: function() {
        MODx.config.help_url = 'https://docs.modmore.com/en/Open_Source/Akismet/index.html?embed=1';
        MODx.loadHelpPane();
    },

    donate: function() {
        window.open('https://modmore.com/extras/akismet/donate/');
    }
});
Ext.reg('akismet-page-home',Akismet.page.Home);