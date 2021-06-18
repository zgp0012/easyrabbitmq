<?php
namespace mq\obj;

use mq\abs as MA;
use PhpAmqpLib\Message as PM;

class FanoutPublisherObject extends MA\MqPublisherObject
{

    public function bind()
    {
        $this->channel->queue_bind($this->queue, $this->exchange);
    }

    public function publish($message, $config = null)
    {
        //暂不支持$config配置
        $objMessage = new PM\AMQPMessage($message);
        $this->channel->basic_publish($objMessage, $this->exchange);
    }
}