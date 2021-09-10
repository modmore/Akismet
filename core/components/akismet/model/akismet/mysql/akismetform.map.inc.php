<?php
/**
 * Akismet for MODX
 *
 * Copyright 2021 by modmore 
 *
 * @package akismet
 * @license See core/components/akismet/docs/license.txt
 */

$xpdo_meta_map['AkismetForm']= array (
  'package' => 'akismet',
  'version' => '1.1',
  'table' => 'akismet_forms',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'reported_status' => 'unknown',
    'manual_status' => '',
    'blog' => '',
    'comment_type' => '',
    'comment_author' => '',
    'comment_author_email' => '',
    'comment_author_url' => '',
    'comment_content' => NULL,
    'user_ip' => '',
    'user_agent' => '',
    'referrer' => '',
    'permalink' => '',
    'blog_charset' => '',
    'blog_lang' => '',
    'recheck_reason' => '',
    'user_role' => '',
    'is_test' => 0,
    'comment_date_gmt' => NULL,
    'comment_modified_gmt' => NULL,
    'honeypot_field_name' => '',
    'honeypot_field_value' => '',
    'created_at' => 'CURRENT_TIMESTAMP',
    'updated_at' => NULL,
  ),
  'fieldMeta' => 
  array (
    'reported_status' => 
    array (
      'dbtype' => 'enum',
      'precision' => '\'unknown\',\'spam\',\'notspam\'',
      'phptype' => 'string',
      'null' => false,
      'default' => 'unknown',
    ),
    'manual_status' => 
    array (
      'dbtype' => 'enum',
      'precision' => '\'\',\'spam\',\'notspam\'',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'blog' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '191',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'comment_type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'comment_author' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '191',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'comment_author_email' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '191',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'comment_author_url' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '191',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'comment_content' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'user_ip' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'user_agent' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '191',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'referrer' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '191',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'permalink' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '191',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'blog_charset' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '191',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'blog_lang' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '191',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'recheck_reason' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '191',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'user_role' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '191',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'is_test' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'comment_date_gmt' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'string',
      'null' => true,
    ),
    'comment_modified_gmt' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'string',
      'null' => true,
      'default' => NULL,
    ),
    'honeypot_field_name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '191',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'honeypot_field_value' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '191',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'created_at' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => false,
      'default' => 'CURRENT_TIMESTAMP',
    ),
    'updated_at' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'reported_status' => 
    array (
      'alias' => 'reported_status',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'reported_status' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'manual_status' => 
    array (
      'alias' => 'manual_status',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'manual_status' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'comment_type' => 
    array (
      'alias' => 'comment_type',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'comment_type' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'comment_author' => 
    array (
      'alias' => 'comment_author',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'comment_author' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'comment_author_email' => 
    array (
      'alias' => 'comment_author_email',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'comment_author_email' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'comment_author_url' => 
    array (
      'alias' => 'comment_author_url',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'comment_author_url' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'user_ip' => 
    array (
      'alias' => 'user_ip',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'user_ip' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'user_agent' => 
    array (
      'alias' => 'user_agent',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'user_agent' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'referrer' => 
    array (
      'alias' => 'referrer',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'referrer' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'permalink' => 
    array (
      'alias' => 'permalink',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'permalink' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'blog_charset' => 
    array (
      'alias' => 'blog_charset',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'blog_charset' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'blog_lang' => 
    array (
      'alias' => 'blog_lang',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'blog_lang' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'user_role' => 
    array (
      'alias' => 'user_role',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'user_role' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'is_test' => 
    array (
      'alias' => 'is_test',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'is_test' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'comment_date_gmt' => 
    array (
      'alias' => 'comment_date_gmt',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'comment_date_gmt' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
    'comment_modified_gmt' => 
    array (
      'alias' => 'comment_modified_gmt',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'comment_modified_gmt' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
    'honeypot_field_name' => 
    array (
      'alias' => 'honeypot_field_name',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'honeypot_field_name' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
    'honeypot_field_value' => 
    array (
      'alias' => 'honeypot_field_value',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'honeypot_field_value' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
    'created_at' => 
    array (
      'alias' => 'created_at',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'created_at' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'updated_at' => 
    array (
      'alias' => 'updated_at',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'updated_at' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
  ),
);
