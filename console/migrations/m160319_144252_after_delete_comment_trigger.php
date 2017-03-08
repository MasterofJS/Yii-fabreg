<?php

use console\migrations\Migration;

class m160319_144252_after_delete_comment_trigger extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TRIGGER :trigger
AFTER DELETE ON {{%comment}}
FOR EACH ROW
BEGIN
  DELETE FROM {{%notification}} WHERE [[entity_id]] = OLD.[[id]] AND [[entity_type]] = :type;
  DELETE FROM {{%like}} WHERE [[entity_id]] = OLD.[[id]] AND [[entity_type]] = :type;
END
SQL;
        $this->execute(strtr($sql, [
            ':type' => \common\models\Comment::TYPE,
            ':trigger' => self::TRIGGER_AFTER_DELETE_COMMENT,
            '{{%comment}}' => $this->db->quoteTableName(self::TABLE_COMMENT),
            '{{%notification}}' => $this->db->quoteTableName(self::TABLE_NOTIFICATION),
            '{{%like}}' => $this->db->quoteTableName(self::TABLE_LIKE),
        ]));
    }

    public function down()
    {
        $this->dropTrigger(self::TRIGGER_AFTER_DELETE_COMMENT);
    }

}
