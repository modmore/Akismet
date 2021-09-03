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
            },
            {
                header: _('akismet.corrected'),
                dataIndex: 'manual_status',
                sortable: true,
                width: 50,
            },
            {
                header: _('akismet.name'),
                dataIndex: 'comment_author',
                sortable: true,
                width: 100,
            },
            {
                header: _('akismet.email'),
                dataIndex: 'comment_author_email',
                sortable: true,
                width: 100,
            },
            {
                header: _('akismet.content'),
                dataIndex: 'comment_content',
                sortable: true,
                width: 200,
            },
        ]
    }
});
Ext.reg('akismet-grid-forms', Akismet.grid.Forms);