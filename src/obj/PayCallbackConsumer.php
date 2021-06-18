<?php
namespace mq\obj;

use mq\abs\MqConsumerObject;

class PayCallbackConsumer extends MqConsumerObject
{

    public function run($message)
    {
        //todo 业务逻辑代码
        //消费者消费一次完毕后就不再消费
//        $this->disableBlocking();
        //return true 代表消息处理完毕
        //return false 代表消息处理异常，不正真消费
    }

}