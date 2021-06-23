<?php
namespace Mq\obj;

use Mq\abs\MqConsumerObject;
use PhpAmqpLib\Wire\AMQPTable;

class DelayedConsumer extends MqConsumerObject
{
    public function init($exchange = "",$queue = "",$bind = "")
    {
        $this->setExchange($exchange)->setQueue($queue)->setBindKey($bind);
        $this->channel->exchange_declare(
            $this->exchange,
            'x-delayed-message',
            false,
            true,
            false,
            false,
            false,
            new AMQPTable([
                "x-delayed-type" => 'direct'
            ])
        );
        //队列声明
        $this->channel->queue_declare($this->queue, false, true, false, false);
        //队列与exchange绑定
        $this->channel->queue_bind($this->queue, $this->exchange, $this->bindKey);
        return $this;
    }

    public function run($msg)
    {
        //todo 业务逻辑代码
        //消费者消费一次完毕后就不再消费
        //$this->disableBlocking();
        //return true 代表消息处理完毕
        //return false 代表消息处理异常，不正真消费
        return true;
    }

}