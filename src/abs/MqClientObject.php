<?php
namespace Mq\abs;

use PhpAmqpLib\Connection as MQC;
use PhpAmqpLib\Exception\AMQPHeartbeatMissedException;

abstract class MqClientObject
{

    public $channel;

    private static $connect;

    private static $config;

    private static $lastConnectTime;

    public function __construct(array $config)
    {
        if(!(self::$connect instanceof MQC\AMQPStreamConnection)) {
            self::connect($config);
        }
        if(!self::$config) {
            self::$config = $config;
        }
        $this->channel = self::$connect->channel();
    }

    private static function connect(array $config)
    {
        self::$connect = new MQC\AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password'],
            isset($config['vhost']) && !empty($config['vhost']) ? $config['vhost'] : '/'
        );
        self::$lastConnectTime = time();
    }

    public static function close()
    {
        try{
            self::$connect->close();
            self::$connect = null;
            self::$config = null;
        } catch (\Exception $ex) {
            return false;
        }
        return true;
    }

    public function createTempQueue()
    {
        list($queue, ) = $this->channel->queue_declare("", false, false, true, false);
        return $queue;
    }


    public function ping()
    {
        try{
            self::$connect->checkHeartBeat();
        } catch (AMQPHeartbeatMissedException $MQheartEx) {
            return false;
        } catch(\Exception $ex) {
            //todo log
            exit(0);
        }
        return true;
    }

    public function reConnect()
    {
        self::connect(self::$config);
    }

}