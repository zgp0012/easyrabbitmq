<?php
namespace Mq\abs;


abstract class MqPublisherObject extends MqClientObject
{

    public $exchange;

    public $bindKey;

    public $queue;

    public function setExchange($exchange)
    {
        $this->exchange = $exchange;
        return $this;
    }

    public function setBindKey($bindKey)
    {
        $this->bindKey = $bindKey;
        return $this;
    }

    public function setQueue($queue)
    {
        $this->queue = $queue;
        return $this;
    }

    public abstract function bind();

    public abstract function publish($message, $config = null);

}