<?php


namespace console\models;

use console\helpers\WSCommonHelper;
use Yii;
use Ratchet\ConnectionInterface;
use yii\base\Model;
use common\models\User;

class SocketSession extends Model
{
    const SESSION_TTL = 10 * 60;

    /**
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $token;

    /**
     * @var array
     */
    public $data;
    /**
     * @var ConnectionInterface $connection
     */

    protected $client;

    /**
     * SocketSession constructor.
     *
     * @param ConnectionInterface|null $client
     * @param array                    $config
     */
    public function __construct(ConnectionInterface $client = null, $config = [])
    {
        parent::__construct($config);
        $this->client = $client;
    }

    /**
     * @return ConnectionInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param $client
     *
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return bool
     */
    public function isGuest()
    {
        $user = $this->getUser();

        return !isset($user);
    }

    /**
     * @param int    $state
     * @param string $msg
     *
     * @return ConnectionInterface
     */
    public function sendState(int $state, string $msg)
    {
        $data = WSCommonHelper::buildStateMessage($state, $msg);

        return $this->client->send($data);
    }

    /**
     * @param string $msg
     *
     * @return ConnectionInterface
     */
    public function sendRefresh(string $msg = '')
    {
        $data = WSCommonHelper::buildMessage(WSCommonHelper::STATUS_OK, $msg, WSCommonHelper::ACTION_REFRESH);

        return $this->client->send($data);
    }

    public function close()
    {
        $this->client->close();
    }

    /**
     * @return User|\yii\web\IdentityInterface|null
     */
    protected function getUser()
    {
        $cache = Yii::$app->cache;

        $userId = $cache->get($this->token);

        if ($userId === false) {
            return null;
        }

        $cache->set($userId, static::SESSION_TTL);

        return User::findIdentity($userId);
    }
}
