<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/23/16
 * Time: 4:50 PM
 */

namespace console\migrations;


/**
 * Class Migration
 * @package console\migrations\Migrate
 *
 * @property string tableOptions
 */
class Migration extends \yii\db\Migration
{
    const TABLE_IMAGE = '{{%image}}';
    const TABLE_USER = '{{%user}}';
    const TABLE_USER_AUTH = '{{%auth}}';
    const TABLE_POST = '{{%post}}';
    const TABLE_COMMENT = '{{%comment}}';
    const TABLE_LIKE = '{{%like}}';
    const TABLE_SHARE = '{{%share}}';
    const TABLE_NOTIFICATION = '{{%notification}}';
    const TABLE_TASK = '{{%task}}';

    const TABLE_POST_REPORT = '{{%post_report}}';
    const TABLE_COMMENT_REPORT = '{{%comment_report}}';
    const TABLE_USER_MUTE = '{{%user_mute}}';
    const TABLE_VARIABLE = '{{%variable}}';
    const TABLE_USER_SESSION = '{{%session}}';
    const TABLE_ADMIN_SESSION = '{{%admin_session}}';

    const TABLE_ADMIN = '{{%admin}}';

    const TRIGGER_AFTER_DELETE_POST = 'after_delete_post';
    const TRIGGER_AFTER_DELETE_COMMENT = 'after_delete_comment';

    public function getTableOptions()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {

            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        return $tableOptions;
    }

    public function addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        if (null === $name) {
            $altColumns = $columns;
            if (is_array($altColumns)) {
                $altColumns = implode('-', $altColumns);
            }

            $name = 'fk-' . trim($table, '{}%') . '-' . $altColumns;
        }
        parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
    }

    /**
     * @inheritdoc
     */
    public function createIndex($name, $table, $columns, $unique = false)
    {
        if (null === $name) {
            $altColumns = $columns;
            if (is_array($altColumns)) {
                $altColumns = implode('-', $altColumns);
            }

            $name = 'idx-' . trim($table, '{}%') . '-' . $altColumns;
        }
        parent::createIndex($name, $table, $columns, $unique);
    }

    /**
     * @inheritdoc
     */
    public function addPrimaryKey($name, $table, $columns)
    {
        if (null === $name) {
            $altColumns = $columns;
            if (is_array($altColumns)) {
                $altColumns = implode('-', $altColumns);
            }

            $name = 'pk-' . trim($table, '{}%') . '-' . $altColumns;
        }
        parent::addPrimaryKey($name, $table, $columns);
    }

    public function dropTrigger($trigger)
    {
        $trigger = $this->db->quoteTableName($trigger);
        $this->execute("DROP TRIGGER IF EXISTS $trigger");
    }

}