<?php

use console\migrations\Migration;

class m160319_144238_after_delete_post_trigger extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TRIGGER :trigger
AFTER DELETE ON {{%post}}
FOR EACH ROW
BEGIN
  DELETE FROM {{%notification}} WHERE [[entity_id]] = OLD.[[id]] AND [[entity_type]] = :type;
  DELETE FROM {{%like}} WHERE [[entity_id]] = OLD.[[id]] AND [[entity_type]] = :type;
END
SQL;
        $this->execute(strtr($sql, [
            ':type' => \common\models\Post::TYPE,
            ':trigger' => self::TRIGGER_AFTER_DELETE_POST,
            '{{%post}}' => $this->db->quoteTableName(self::TABLE_POST),
            '{{%notification}}' => $this->db->quoteTableName(self::TABLE_NOTIFICATION),
            '{{%like}}' => $this->db->quoteTableName(self::TABLE_LIKE),
        ]));
    }

    public function down()
    {
        $this->dropTrigger(self::TRIGGER_AFTER_DELETE_POST);
    }
}
