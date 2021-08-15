<?php
// +----------------------------------------------------------------------
// | Message
// +----------------------------------------------------------------------
// | User: Lengnuan <25314666@qq.com>
// +----------------------------------------------------------------------
// | Date: 2021年08月15日
// +----------------------------------------------------------------------

namespace lengnuan\message;

use Yii;
use yii\db\Exception;
use yii\base\Component;

class Message extends Component
{
    /**
     * @var string cache
     */
    public $cache = 'cache';

    /**
     * @var int cache time, if value equal 0,disable cache
     */
    public $cacheTime = 60 * 10;

    /**
     * @var string cache prefix
     */
    public $cachePrefix = 'lengnuan\message#';

    private static $userName = [];

    public function findModel()
    {
        return new \lengnuan\message\models\Message();
    }

    /**
     * 发送一条消息
     * @param $toUid
     * @param $title
     * @param $message
     * @param int $type
     * @param null $fromUid
     * @return array
     * @throws Exception
     */
    public function send($toUid, $title, $message, $type = 1, $fromUid = null)
    {
        $model                  = $this->findModel();
        $model->from            = is_null($fromUid) ? $fromUid = Yii::$app->user->id : $fromUid;
        $model->to              = $toUid;
        $model->dialogue_hash   = $model::calHash($fromUid, $toUid);
        $model->title           = $title;
        $model->message         = $message;
        $model->reply_id        = 0;
        $model->type            = $type;
        $model->status          = $model::$status['UNREAD'];
        $model->created_time    = time();
        if (!$model->save()) {
            throw new Exception('Save failed.', $model->getErrors(), 500);
        }
        return $model->attributes;
    }

    /**
     * 回复一条消息
     * @param $messageId
     * @param $message
     * @return array
     * @throws Exception
     */
    public function reply($messageId, $message)
    {
        $model                = $this->findModel();
        $messageModel         = $model->findOne($messageId);
        $model->from          = $messageModel->to;
        $model->to            = $messageModel->from;
        $model->dialogue_hash = $messageModel->dialogue_hash;
        $model->title         = '';
        $model->message       = $message;
        $model->reply_id      = $messageId;
        $model->type          = $messageModel->type;
        $model->status        = $model::$status['UNREAD'];
        $model->created_time  = time();
        if (!$model->save()) {
            throw new Exception('Save failed.', $model->getErrors(), 500);
        }
        return $model->attributes;
    }

    /**
     * 返回一条消息
     *
     * @param $messageId
     * @return array
     * @throws \Exception
     */
    public function getMessage($messageId)
    {
        $model = $this->findModel()->findOne($messageId);
        if (empty($model)) {
            throw new \Exception('Can not find this message', 500);
        }
        $data  = $model->attributes;
        $data['from_name'] = $this->getUsername($model->from);
        $data['to_name']   = $this->getUsername($model->to);
        return $data;
    }

    /**
     * 获取列表
     * @param $userId
     * @param string $cate
     * @param int $page
     * @param int $limit
     * @param array $orderBy
     * @return array
     */
    public function messageList($userId, $status = null, $cate = 'to', $page = 1, $limit = 20, $orderBy = ['id' => SORT_DESC])
    {
        $model = $this->findModel();
        $query = $model->query(['and', [$cate => $userId], ['status' => $status == null ? 0 : $status], ['reply_id' => 0]]);
        $list  = $query->orderBy($orderBy)->offset(($page - 1) * $limit)->limit($limit)->asArray()->All();
        foreach ($list as &$val) {
            $val['from_name'] = $this->getUsername($val['from']);
            $val['to_name']   = $this->getUsername($val['to']);
        }
        return ['pageTotal' => ceil($query->count() / $limit), 'list' => $list];
    }

    /**
     * 获取对话中的消息列表
     * @param $dialogueHash
     * @return mixed
     */
    public function dialogueMessageList($dialogueHash)
    {
        $model = $this->findModel();
        return $model->query([
            'and', ['dialogue_hash' => $dialogueHash], ['<', 'status', $model::$status['DELETED']]])->orderBy(['id' => SORT_ASC])->asArray()->All();
    }

    /**
     * 删除一条消息(软删除)
     * @param $dialogueHash
     * @return int
     */
    public function del($dialogueHash)
    {
        $model = $this->findModel();
        return $model->updateAll(['status' => $model::$status['DELETED']], ['dialogue_hash' => $dialogueHash]);
    }

    /**
     * 设置消息已读状态
     * @param $dialogueHash
     * @return int
     */
    public function setMessageRead($dialogueHash)
    {
        $model = $this->findModel();
        return $model->updateAll(['status' => $model::$status['READ']], ['dialogue_hash' => $dialogueHash]);
    }

    /**
     * 获取用户名
     * @param $userId
     * @return mixed
     */
    public function getUsername($userId)
    {
        $disable    = $this->cacheTime == 0;
        $cache      = $this->cache;
        $cacheKey   = $this->cachePrefix . $userId;
        $cacheGroup = 'group';
        if ($disable || !isset(self::$userName[$cacheGroup][$userId])) {
            $cacheName = Yii::$app->$cache->get($cacheKey);
            if ($disable || empty($cacheName)) {
                $model = Yii::$app->user->identityClass;
                $user  = $model::findOne($userId);
                $name  = !is_null($user) ? $user->username : $userId;
                Yii::$app->$cache->set($cacheKey, $name, $this->cacheTime);
                self::$userName[$cacheGroup][$userId] = $name;
            } else {
                self::$userName[$cacheGroup][$userId] = $cacheName;
            }
        }
        return self::$userName[$cacheGroup][$userId];
    }
}
