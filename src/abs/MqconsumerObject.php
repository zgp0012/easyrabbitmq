<?php
namespace mq\abs;

abstract class MqConsumerObject extends MqClientObject
{

    public $queue;

    private $blocking = true;

    private $qos = true;

    private $ack = false;

    private $qosCount = 1;

    public function setQueue($queue)
    {
        $this->queue = $queue;
        return $this;
    }

    public function disableAck()
    {
        $this->ack = true;
    }

    public function disablePos()
    {
        $this->qos = false;
    }

    public function setQosCount($count)
    {
        $this->qosCount = $count;
    }

    public function disableBlocking()
    {
        $this->blocking = false;
    }

    //默认的消费方式
    public function consume($failClose = false)
    {
        if($this->qos) {
            $this->channel->basic_qos(null, $this->qosCount, null);
        }
        $this->channel->basic_consume(
            $this->queue,
            '',
            false,
            $this->ack,
            false,
            false,
            [$this, 'call']
        );

        while(count($this->channel->callbacks)) {
            $this->channel->wait();
            if(!$this->blocking) {
                $this->channel->close();
            }
        }
    }


    //回调函数
    public function call($event)
    {
        $message = $event->body;
        $channel = $event->delivery_info['channel'];
        if($this->run($message)) {
            $channel->basic_ack($event->delivery_info['delivery_tag']);
        }
    }


    public function closeChannel()
    {
        try{
            $this->channel->close();
            $this->channel = null;
        } catch (\Exception $ex) {
            return false;
        }
        return true;
    }

    //业务处理
    public abstract function run($message);

    public function __destruct()
    {
        if($this->channel != null) {
            $this->closeChannel();
        }
    }
}