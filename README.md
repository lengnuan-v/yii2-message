# yii2-message
A simple private message extension for yii2, only contain model and API, no web interface.


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
 composer require --prefer-dist lengnuan-v/yii2-message "dev-master"
```

or add

```
"lengnuan/yii2-message": "*"
```

to the require section of your `composer.json` file.

Then

```
 php yii migrate/up --migrationPath=@vendor/lengnuan-v/yii2-message/src/migrations
```

Configuration
-----
To use this extension, simply add the following code in your application configuration:
```php
return [
    //....
    'components' => [
        'message' => [
            'class' => 'lengnuan\message\Message',
        ],
    ],
];
```

Usage
-----

Once the extension is installed, simply use it in your code by :

```php
// 发送一条消息.
Yii::$app->message->send($userId = 12, $title = 'How are u?', $message = 'How are u?');

// 回复一条消息
Yii::$app->message->reply($messageId = 2100, $message = 'Fine, and u?');

// 返回一条消息
Yii::$app->message->getMessage($messageId = 2100);

// 获取列表
Yii::$app->message->messageList($userId = 12, $cate = 'to', $page = 1, $limit = 20, $orderBy = ['id' => SORT_DESC]);

// 获取对话中的消息列表
Yii::$app->message->getMessage($dialogueHash = '4795342d74935999cc82ded1b589072c');

// 删除一条消息
Yii::$app->message->del($dialogueHash = '4795342d74935999cc82ded1b589072c');

// 设置消息已读状态
Yii::$app->message->setMessageRead($dialogueHash = '4795342d74935999cc82ded1b589072c');

```
