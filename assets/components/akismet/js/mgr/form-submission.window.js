Akismet.window.FormSubmission = function(config) {
    config = config || {};
    var that = this;

    Ext.applyIf(config,{
        title: 'View Form Submission',
        autoHeight:true,
        modal: true,
        width: 800,
        buttons: [{
            text: _('close'),
            handler: function() {
                that.close();
            }
        }],
        fields: [{
            xtype: 'hidden',
            name: 'id'
        },{
            layout: 'column',
            items: [{
                layout: 'form',
                columnWidth:.65,
                defaults: {
                    cls: 'disabled-field',
                    disabled: true,
                },
                items:[{
                    xtype: 'textfield',
                    fieldLabel: 'When',
                    name: 'created_at',
                    anchor: '100%'
                },{
                    xtype: 'textfield',
                    fieldLabel: 'Author',
                    name: 'comment_author',
                    anchor: '100%'
                },{
                    xtype: 'textfield',
                    fieldLabel: 'Email',
                    name: 'comment_author_email',
                    anchor: '100%'
                },{
                    xtype: 'textfield',
                    fieldLabel: 'URL',
                    name: 'comment_author_url',
                    anchor: '100%'
                },{
                    xtype: 'textarea',
                    fieldLabel: 'Content',
                    name: 'comment_content',
                    anchor: '100%'
                }]
            },{
                columnWidth: .35,
                items:[{
                    html: this.renderData(config),
                    anchor:'100%'
                }]
            }]
        }]
    });
    Akismet.window.FormSubmission.superclass.constructor.call(this,config);
};
Ext.extend(Akismet.window.FormSubmission, MODx.Window, {

    renderData: function(config) {
        return '<div class="data-column">'
            + '<ul>'
            + '<li>' + _('akismet.is_test') + ': ' + config.record['is_test'] + '</li>'
            + '<li>' + _('akismet.analysis') + ': ' + config.record['reported_status'] + '</li>'
            + '<li>' + _('akismet.overridden') + ': ' + config.record['manual_status'] + '</li>'
            + '<li>' + _('akismet.user_agent') + ': ' + config.record['user_agent'] + '</li>'
            + '<li>' + _('akismet.user_ip') + ': ' + config.record['user_ip'] + '</li>'
            + '<li>' + _('akismet.user_role') + ': ' + config.record['user_role'] + '</li>'
            + '<li>' + _('id') + ': ' + config.record['id'] + '</li>'
            + '<li>' + _('akismet.honeypot_field') + ': ' + config.record['honeypot_field'] + '</li>'
            + '<li>' + _('akismet.site') + ': ' + config.record['blog'] + '</li>'
            + '<li>' + _('akismet.permalink') + ': ' + config.record['permalink'] + '</li>'
            + '<li>' + _('akismet.referrer') + ': ' + config.record['referrer'] + '</li>'
            + '<li>' + _('akismet.charset') + ': ' + config.record['blog_charset'] + '</li>'
            + '<li>' + _('akismet.site_lang') + ': ' + config.record['blog_lang'] + '</li>'
            + '<li>' + _('akismet.recheck_reason') + ': ' + config.record['recheck_reason'] + '</li>'
            + '<li>' + _('akismet.updated_at') + ': ' + config.record['recheck_reason'] + '</li>'
            + '</ul>'
            + '</div>';
    }

});
Ext.reg('akismet-window-form-submission', Akismet.window.FormSubmission);