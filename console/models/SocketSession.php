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
    protected $action = '';

    /**
     * @var string
     */
    protected $token = '';

    /**
     * @var array
     */
    protected $data = [];
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
     * @param $data
     *
     * @return $this
     */
    public function setData($msg)
    {
        $this->data = json_decode($msg, true);

        $this->token = $this->data['token'] ?? '';

        $this->action = $this->data['action'] ?? '';

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getToken()
    {
        return $this->token;
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
     */
    public function sendRefresh(string $msg = '')
    {
        $data = WSCommonHelper::buildMessage(WSCommonHelper::STATUS_OK, $msg, WSCommonHelper::ACTION_REFRESH);

        $this->client->send($data);
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

        if ($cache->exists($this->token)) {
            $cache->delete($this->token);
        }
        $cache->add($this->token, $userId);

        return User::findIdentity($userId);
    }
}
