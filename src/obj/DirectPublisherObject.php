<?php
namespace Mq\obj;

use Mq\abs as MA;
use PhpAmqpLib\Message as PM;
use PhpAmqpLib\Wire\AMQPTable;

class DirectPublisherObject extends MA\MqPublisherObject
{

    public function init($exchange = "",$queue = "",$bind = "")
    {
        $this->setExchange($exchange)->setQueue($queue)->setBindKey($bind);
        $this->channel->exchange_declare(
            $this->exchange,
            'direct',
            false,
            true,
            false,
            false,
            false
        );
        //队列声明
        $this->channel->queue_declare($this->queue, false, true, false, false);
        //队列与exchange绑定
        $this->channel->queue_bind($this->queue, $this->exchange, $this->bindKey);
    }

    public function publish($message, $config = null)
    {
        $objMessage = new PM\AMQPMessage($message);
        $this->channel->basic_publish($objMessage, $this->exchange, $this->bindKey);
    }
}
