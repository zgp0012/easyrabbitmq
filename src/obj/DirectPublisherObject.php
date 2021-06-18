<?php
namespace mq\obj;

use mq\abs as MA;
use PhpAmqpLib\Message as PM;

class DirectPublisherObject extends MA\MqPublisherObject
{

    public function bind()
    {
        $this->channel->queue_bind($this->queue, $this->exchange, $this->bindKey);
    }

    public function publish($message, $config = null)
    {
        $objMessage = new PM\AMQPMessage($message);
        $this->channel->basic_publish($objMessage, $this->exchange, $this->bindKey);
    }
}