<?php
// +----------------------------------------------------------------------
// | m210815_134755_message.php
// +----------------------------------------------------------------------
// | User: Lengnuan <25314666@qq.com>
// +----------------------------------------------------------------------
// | Date: 2021年08月15日
// +----------------------------------------------------------------------

use yii\db\Migration;
use yii\db\Schema;

class m210815_134755_message extends Migration
{
    public function up()
    {
        $tableOptions = '';

        if (Yii::$app->db->driverName == 'mysql')
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('{{%message}}', [
            'id'            => Schema::TYPE_PK,
            'reply_id'      => Schema::TYPE_INTEGER,
            'type'          => Schema::TYPE_INTEGER,
            'from'          => Schema::TYPE_INTEGER,
            'to'            => Schema::TYPE_INTEGER,
            'dialogue_hash' => Schema::TYPE_STRING . '(64) NOT NULL',
            'status'        => Schema::TYPE_INTEGER,
            'title'         => Schema::TYPE_STRING . '(200) NOT NULL',
            'message'       => Schema::TYPE_TEXT,
            'created_time'  => Schema::TYPE_INTEGER . '(10) NOT NULL DEFAULT 0',
        ], $tableOptions);
        $this->createIndex('reply_id', '{{%message}}', ['reply_id']);
        $this->createIndex('idx_dialogue_hash', '{{%message}}', ['dialogue_hash']);
        $this->createIndex('idx_from-to', '{{%message}}', ['from', 'to']);
        $this->createIndex('idx_to', '{{%message}}', ['to']);
        $this->createIndex('idx_type', '{{%message}}', ['type']);
        $this->createIndex('idx_status', '{{%message}}', ['status']);
        $this->createIndex('idx_created_time', '{{%message}}', ['created_time']);
        $this->addCommentOnTable('{{%message}}', '消息通知表');
    }

    public function down()
    {
        $this->dropTable('message');
        return false;
    }
}
