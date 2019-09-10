<?php


namespace console\models;

use consik\yii2websocket\events\WSClientEvent;
use Yii;
use Ratchet\ConnectionInterface;
use yii\base\Model;
use common\models\User;

class SocketSession extends Model
{
    /**
     * @var WSClientEvent $connection
     */
    protected $client;

    public function __construct(WSClientEvent $client, $config = [])
    {
        parent::__construct($config);
        $this->client = $client;
    }

    protected function getUser($tokens)
    {
    }
}