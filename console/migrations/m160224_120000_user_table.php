<?php


use console\migrations\Migration;

class m160224_120000_user_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_USER, [
            'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'username' => $this->string(255)->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'password_reset_token' => $this->string(255)->unique(),
            'email_confirmation_token' => $this->string(255)->unique(),
            'email' => $this->string(255)->notNull()->unique(),
            'status' => 'TINYINT UNSIGNED DEFAULT 10',
            'first_name' => $this->string(31)->notNull(),
            'last_name' => $this->string(31)->notNull(),
            'gender' => 'CHAR(1) NOT NULL',
            'country' => 'CHAR(2) NULL',
            'birthday' => $this->date()->defaultValue(null),
            'about' => $this->string(1020)->defaultValue(null),
            'deletion_reason' => $this->string(255)->defaultValue(null),
            'avatar_id' => 'BIGINT UNSIGNED NULL',
            'cover_id' => 'BIGINT UNSIGNED NULL',
            'show_nswf' => $this->boolean()->defaultValue(false),
            'hide_upvotes' => $this->boolean()->defaultValue(false),
            'notify_post_upvote' => $this->boolean()->defaultValue(true),
            'notify_post_comment' => $this->boolean()->defaultValue(true),
            'notify_post_share' => $this->boolean()->defaultValue(true),
            'notify_comment_upvote' => $this->boolean()->defaultValue(true),
            'notify_comment_reply' => $this->boolean()->defaultValue(true),
            'api_usage' => $this->text()->defaultValue(null),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->addForeignKey(null, self::TABLE_USER, 'avatar_id', self::TABLE_IMAGE, 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey(null, self::TABLE_USER, 'cover_id', self::TABLE_IMAGE, 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable(self::TABLE_USER);
    }
}
