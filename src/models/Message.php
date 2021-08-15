<?php
// +----------------------------------------------------------------------
// | Message
// +----------------------------------------------------------------------
// | User: Lengnuan <25314666@qq.com>
// +----------------------------------------------------------------------
// | Date: 2021年08月15日
// +----------------------------------------------------------------------

namespace lengnuan\message\models;

use Yii;

class Message extends \yii\db\ActiveRecord
{
    public static $status = [
        'UNREAD'  => '0',
        'READ'    => '5',
        'DELETED' => '10',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message', 'created_time'], 'required'],
            [['type', 'from', 'to', 'status', 'reply_id'], 'integer'],
            [['title', 'message', 'dialogue_hash'], 'string'],
            [['created_time'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'reply_id'      => 'Reply Id',
            'type'          => 'Type',
            'from'          => 'From',
            'to'            => 'To',
            'dialogue_hash' => 'Dialogue Hash',
            'status'        => 'Status',
            'title'         => 'Title',
            'message'       => 'Message',
            'created_time'  => 'Created Time',
        ];
    }

    /**
     * @param string $fields
     * @param array $condition
     * @return mixed
     */
    public static function query($condition = [], $fields = '*')
    {
        return self::find()->select($fields)->where($condition);
    }

    /**
     * 返回唯一hash值
     * @param $from
     * @param $to
     * @return string
     */
    public static function calHash($from, $to)
    {
        $sort = [(int)$from, (int)$to];
        sort($sort);
        return md5(sprintf('%s%s%s', implode('_', $sort), time(), rand(1, 99999)));
    }

}
