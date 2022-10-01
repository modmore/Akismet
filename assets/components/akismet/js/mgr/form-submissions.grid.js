Akismet.grid.FormSubmissions = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        id: 'akismet-grid-form-submissions',
        url: Akismet.config.connectorUrl,
        baseParams: { action: 'mgr/form-submissions/getlist' },
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
        autoExpandColumn: 'content',
        autoHeight: true,
        columns: this.getColumns(),
        tbar:[{
            xtype: 'akismet-field-search',
            emptyText: _('akismet.search'),
            width: 400,
            grid: this
        }]
    });
    Akismet.grid.FormSubmissions.superclass.constructor.call(this, config);

    this.on('render', function() {
        this.mask = new Ext.LoadMask(this.getEl());
        if (!this.loaded) this.mask.show();
    }, this);

    this.getStore().on('load', function(s) {
        this.loaded = true;
        if (this.mask) this.mask.hide();
    }, this);

    this.on('rowclick', function(grid, rowIndex, event) {
        grid.handleClick(grid, rowIndex, event);
    }, this);

};
Ext.extend(Akismet.grid.FormSubmissions, MODx.grid.Grid, {
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
                fixed: true,
                width: 140,
            },
            {
                header: _('akismet.analysis'),
                dataIndex: 'reported_status',
                fixed: true,
                sortable: true,
                width: 100,
                renderer: this.renderReportedStatus
            },
            {
                header: _('akismet.override'),
                dataIndex: 'manual_status',
                id: 'override-col',
                fixed: true,
                sortable: true,
                width: 100,
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

    search: function (tf, nv, ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
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
                return '<button class="override-btn mark-ham">' + _('akismet.notspam') + '</button>';
            } else {
                return '<button class="override-btn mark-spam">' + _('akismet.spam') + '</button>';
            }
        }
        else {
            var iconType = val === 'spam' ? 'bug' : 'check';
            return '<span class="override-span"><i class="icon icon-' + iconType + '"></i> '
                + '&nbsp;' + _('akismet.' + val) + '</span>';
        }
    },

    handleClick: function(grid, rowIndex, event) {
        var elm = Ext.get(event.target);
        if (elm.hasClass('mark-spam')) {
            grid.markAsSpam(grid.getStore().getAt(rowIndex));
        }
        else if (elm.hasClass('mark-ham')) {
            grid.markAsHam(grid.getStore().getAt(rowIndex));
        }

        return false;
    },

    getMenu: function() {
        return [{
            text: _('akismet.form_view'),
            handler: this.viewFormSubmission
        },'-',{
            text: _('akismet.remove_form_submission'),
            handler: this.removeForm
        }];
    },

    markAsSpam: function(record) {
        MODx.msg.confirm({
            title: _('akismet.override_as_spam')
            ,text: _('akismet.confirm_override_as_spam')
                + '<div class="akismet-notice">' + _('akismet.override_notice') + '</div>'
            ,url: this.config.url
            ,params: {
                action: 'mgr/form-submissions/markspam'
                ,id: record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    },

    markAsHam: function(record) {
        MODx.msg.confirm({
            title: _('akismet.override_as_not_spam')
            ,text: _('akismet.confirm_override_as_not_spam')
                + '<div class="akismet-notice">' + _('akismet.override_notice') + '</div>'
            ,url: this.config.url
            ,params: {
                action: 'mgr/form-submissions/markham'
                ,id: record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    },

    viewFormSubmission: function(btn, e) {
        if (!this.menu.record || !this.menu.record.id) return false;
        var formSubmission = MODx.load({
            xtype: 'akismet-window-form-submission'
            ,record: this.menu.record
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });
        formSubmission.fp.getForm().reset();
        formSubmission.fp.getForm().setValues(this.menu.record);
        formSubmission.show(e.target);
    },

    removeForm: function() {
        MODx.msg.confirm({
            title: _('akismet.remove_form_submission')
            ,text: _('akismet.confirm_remove_form_submission')
            ,url: this.config.url
            ,params: {
                action: 'mgr/form-submissions/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
});
Ext.reg('akismet-grid-form-submissions', Akismet.grid.FormSubmissions);

/**
 * @param config
 * @constructor
 */
Akismet.field.Search = function(config) {
    config = config || {};
    var grid = config.grid || null

    Ext.applyIf(config, {
        xtype: 'trigger',
        name: 'query',
        emptyText: _('akismet.search'),
        width: 250,
        ctCls: 'akismet-search',
        onTriggerClick: function() {
            this.reset();
            this.fireEvent('click');
        },
        listeners: {
            'render': {
                fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER,
                        fn: function() {
                            grid.search(this);
                            return true;
                        },
                        scope: cmp
                    });
                },
                scope:grid
            },
            'click': {
                fn: function(trigger) {
                    grid.getStore().setBaseParam('query', '');
                    grid.getStore().load();
                },
                scope: grid
            }
        }
    });
    Akismet.field.Search.superclass.constructor.call(this,config);
};
Ext.extend(Akismet.field.Search, Ext.form.TriggerField);
Ext.reg('akismet-field-search', Akismet.field.Search);
