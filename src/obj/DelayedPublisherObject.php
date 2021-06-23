<?php
namespace Mq\obj;

use Mq\abs as MA;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class DelayedPublisherObject extends MA\MqPublisherObject
{
    public $message;

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
    }

    public function publish($message, $second = 0)
    {
        $this->createDelayMsg($message,$second);
        $this->channel->basic_publish($this->message, $this->exchange, $this->bindKey);
    }

    private function createDelayMsg($message ,$second = 0)
    {
        $this->message = new AMQPMessage(
            $message,
            [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                //此处是重点，设置延时时间，单位是毫秒 1s=1000ms,实例延迟20s
                'application_headers' => new AMQPTable([
                    'x-delay' => $second,
                ])
            ]
        );
    }
}