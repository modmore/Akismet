Akismet.grid.Forms = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        id: 'akismet-grid-forms',
        url: Akismet.config.connectorUrl,
        baseParams: { action: 'mgr/forms/getList' },
        fields: [
            'id',
            'created_at',
            'reported_status',
            'manual_status',
            'blog',
            'comment_type',
            'comment_author',
            'comment_author_email',
            'comment_author_url',
            'comment_content',
            'user_ip',
            'user_agent',
            'referrer',
            'permalink',
            'blog_charset',
            'blog_lang',
            'recheck_reason',
            'user_role',
            'is_test',
            'comment_date_gmt',
            'comment_modified_gmt',
            'honeypot_field',
            'updated_at'
        ],
        paging: true,
        remoteSort: true,
        anchor: '97%',
        autoExpandColumn: 'name',
        columns: this.getColumns()
    });
    Akismet.grid.Forms.superclass.constructor.call(this, config);
};
Ext.extend(Akismet.grid.Forms, MODx.grid.Grid, {
    getColumns: function() {
        return [
            {
                header: _('id'),
                dataIndex: 'id',
                hidden: true
            },
            {
                header: _('akismet.when'),
                dataIndex: 'created_at',
                sortable: true,
                width: 60,
            },
            {
                header: _('akismet.result'),
                dataIndex: 'reported_status',
                sortable: true,
                width: 50,
                renderer: this.renderReportedStatus
            },
            {
                header: _('akismet.override'),
                dataIndex: 'manual_status',
                sortable: true,
                width: 80,
                align: 'center',
                renderer: this.renderOverrideStatus
            },
            {
                header: _('akismet.name'),
                dataIndex: 'comment_author',
                sortable: true,
                width: 80,
            },
            {
                header: _('akismet.email'),
                dataIndex: 'comment_author_email',
                sortable: true,
                width: 80,
            },
            {
                header: _('akismet.content'),
                dataIndex: 'comment_content',
                sortable: true,
                width: 200,
            },
        ]
    },

    renderReportedStatus: function(val, meta, rec) {
        if (rec.get('manual_status') !== '') {
            return '<span class="overridden">' + _('akismet.' + val) + '</span>';
        }

        var icon = val === 'spam' ? '<i class="icon icon-bug"></i> ' : '<i class="icon icon-check"></i> ';
        return icon + '&nbsp;' + _('akismet.' + val);
    },

    renderOverrideStatus: function(val, meta, rec) {
        if (val === '') {
            if (rec.get('reported_status') === 'spam') {
                return '<button class="override-btn" data-mark-ham="true">' + _('akismet.mark_as_not_spam') + '</button>';
            } else {
                return '<button class="override-btn" data-mark-spam="true">' + _('akismet.mark_as_spam') + '</button>';
            }
        }
        else {
            if (val === 'spam') {
                return '<i class="icon icon-bug"></i> ' + '&nbsp;' + _('akismet.' + val);
            }
            else {
                return '<i class="icon icon-check"></i> ' + '&nbsp;' + _('akismet.' + val);
            }
        }
    },

    getMenu: function() {
        return [{
            text: _('akismet.form_view'),
            handler: this.viewForm
        },'-',{
            text: _('akismet.remove_form_submission'),
            handler: this.removeForm
        }];
    },

    markAsSpam: function() {
        MODx.msg.confirm({
            title: _('akismet.override_as_spam')
            ,text: _('akismet.confirm_override_as_spam')
            ,url: this.config.url
            ,params: {
                action: 'mgr/forms/markspam'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    },

    markAsHam: function() {
        MODx.msg.confirm({
            title: _('akismet.override_as_not_spam')
            ,text: _('akismet.confirm_override_as_not_spam')
            ,url: this.config.url
            ,params: {
                action: 'mgr/forms/markham'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    },

    removeForm: function() {
        MODx.msg.confirm({
            title: _('akismet.remove_form_submission')
            ,text: _('akismet.confirm_remove_form_submission')
            ,url: this.config.url
            ,params: {
                action: 'mgr/forms/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
});
Ext.reg('akismet-grid-forms', Akismet.grid.Forms);