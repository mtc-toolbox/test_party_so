<?php


namespace console\daemons;

use common\helpers\AppleCommonHelper;
use common\models\Apple;
use console\models\SocketSession;
use consik\yii2websocket\WebSocketServer;
use Ratchet\ConnectionInterface;
use yii\helpers\Console;
use yii\helpers\Json;

use console\helpers\WSCommonHelper;

/**
 * Class CommandsServer
 * @package console\daemons
 */
class CommandsServer extends WebSocketServer
{
    const COMMANDS_FALL = 'fall';
    protected $sessions;
    /**
     * CommandsServer constructor.
     */
    public function __construct()
    {
        $this->sessions = new \SplObjectStorage();

        $this->closeConnectionOnError = false;

        Console::stdout('WS server started' . PHP_EOL);
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        Console::stdout('Connection Open' . PHP_EOL);

        $session = new SocketSession($conn);

        $this->sessions->attach($session);
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        Console::stdout('Connection Closed' . PHP_EOL);

        $session = $this->prepareSession($conn, []);

        if (isset($session)) {
            $this->deleteSession($session);
        }
    }

    /**
     * @param ConnectionInterface $from
     * @param                     $msg
     *
     * @return string|null
     */
    protected function getCommand(ConnectionInterface $from, $msg)
    {
        $currentCommand = static::getCommandName($msg) ?? parent::getCommand($from, $msg);
        Console::stdout("Connection get command {$currentCommand}" . PHP_EOL);

        return $currentCommand;
    }

    /**
     * @param string $message
     *
     * @return string|null
     */
    protected function getCommandName(string $message)
    {
        $request = Json::decode($message, true);

        return $request['action'] ?? null;
    }

    /**
     * @param ConnectionInterface $client
     * @param                     $msg
     *
     * @throws \yii\base\Exception
     */
    protected function commandGenerate(ConnectionInterface $client, $msg)
    {
        Console::stdout("Connection run command generate" . PHP_EOL);

        $session = $this->prepareSession($client, $msg);

        if (!isset($session)) {
            Console::stdout("Generate command failed" . PHP_EOL);
            return;
        }

        Console::stdout("Create apples" . PHP_EOL);

        if (!AppleCommonHelper::generateApples()) {
            Console::stdout("Session could not generate apples" . PHP_EOL);
            $session->sendState(WSCommonHelper::STATUS_UNKNOWN, WSCommonHelper::STATUS_TEXT_UNKNOWN);

            return;
        }

        $this->sendRefresh();

        Console::stdout("Connection done command generate" . PHP_EOL);

    }

    protected function commandFall(ConnectionInterface $client, $msg)
    {
        Console::stdout("Connection run command fall" . PHP_EOL);

        $session = $this->prepareSession($client, $msg);

        if (!isset($session)) {
            Console::stdout("Fall command failed" . PHP_EOL);
            return;
        }

        $data = $session->getData();

        if (!isset($data['data']['id'])) {
            Console::stdout("Fall command failed" . PHP_EOL);
            return;
        }

        $model = Apple::find()->andWhere(['id' => $data['data']['id']])->one();

        if (!isset($model)) {
            Console::stdout("Fall command failed" . PHP_EOL);
            return;
        }

        if (!$model->fall()->save()) {
            $session->sendState(WSCommonHelper::STATUS_UNKNOWN, WSCommonHelper::STATUS_TEXT_UNKNOWN);
            Console::stdout("Fall command failed" . PHP_EOL);
            return;
        };

        $this->sendRedraw($model);

        Console::stdout("Connection done command fall" . PHP_EOL);

    }

    protected function commandEat(ConnectionInterface $client, $msg)
    {
        Console::stdout("Connection run command eat" . PHP_EOL);

        $session = $this->prepareSession($client, $msg);

        if (!isset($session)) {
            Console::stdout("Eat command failed" . PHP_EOL);
            return;
        }

        $data = $session->getData();

        if (!isset($data['data']['id']) || !$data['data']['percent']) {
            Console::stdout("Eat command failed" . PHP_EOL);
            return;
        }

        $model = Apple::find()->andWhere(['id' => $data['data']['id']])->one();

        if (!isset($model)) {
            Console::stdout("Eat command failed" . PHP_EOL);
            return;
        }

        if (!$model->canEat() || !$model->eat($data['data']['percent'])->save()) {
            $session->sendState(WSCommonHelper::STATUS_UNKNOWN, WSCommonHelper::STATUS_TEXT_UNKNOWN);
            Console::stdout("Eat command failed" . PHP_EOL);
            return;
        };

        $this->sendRedraw($model);

        Console::stdout("Connection done command eat" . PHP_EOL);

    }

    protected function commandRedraw(ConnectionInterface $client, $msg)
    {
        Console::stdout("Connection run command redraw" . PHP_EOL);

        $session = $this->prepareSession($client, $msg);

        if (!isset($session)) {
            Console::stdout("Redraw command failed" . PHP_EOL);
            $session->sendState(WSCommonHelper::STATUS_UNKNOWN, WSCommonHelper::STATUS_TEXT_UNKNOWN);
            return;
        }

        $data = $session->getData();

        if (!isset($data['data']['id'])) {
            Console::stdout("Redraw command failed" . PHP_EOL);
            return;
        }

        $model = Apple::find()->andWhere(['id' => $data['data']['id']])->one();

        if (!isset($model)) {
            Console::stdout("Redraw command failed" . PHP_EOL);
            return;
        }

        $this->sendRedraw($model);


        Console::stdout("Connection done command redraw" . PHP_EOL);
    }

    /**
     * @param ConnectionInterface $from
     *
     * @return SocketSession|object|null
     */
    protected function findSession(ConnectionInterface $from)
    {
        /* @var SocketSession $session */
        foreach ($this->sessions as $session) {
            $client = $session->getClient();
            if ($client === $from) {
                return $session;
            }
        }

        return null;
    }

    /**
     * @param SocketSession $session
     */
    protected function deleteSession(SocketSession $session)
    {
        $token = $session->getToken();

        if (isset($token)) {
            $cache = Yii::$app->cache;
            $cache->delete($token);
        }
        /* @var SocketSession $session */
        $this->sessions->detach($session);
    }

    /**
     * @param ConnectionInterface $from
     *
     * @return array
     */
    protected function getOtherSessions(ConnectionInterface $from)
    {
        $result = [];

        /* @var SocketSession $session */
        foreach ($this->sessions as $session) {
            $client = $session->getClient();
            if ($client !== $from) {
                $result[] = $session;
            }
        }

        return $result;
    }

    /**
     * @param ConnectionInterface $client
     * @param                     $msg
     *
     * @return SocketSession|object|null
     */
    protected function prepareSession(ConnectionInterface $client, $msg)
    {
        $session = $this->findSession($client);

        if (!isset($session)) {
            Console::stdout("Session not found. Create new." . PHP_EOL);
            $session->sendRefresh(WSCommonHelper::STATUS_TEXT_DENIED);
            $session->close();
            $this->deleteSession($session);

            return null;
        }

        Console::stdout("Getting token" . PHP_EOL);

        $session->setData($msg);

        Console::stdout("Check user" . PHP_EOL);

        if ($session->isGuest()) {
            Console::stdout("Session is for guest" . PHP_EOL);
            $session->sendRefresh(WSCommonHelper::STATUS_TEXT_DENIED);
            $session->close();
            $this->deleteSession($session);

            return null;
        }

        return $session;
    }

    /**
     * @param Apple $model
     */
    protected function sendRedraw(Apple $model)
    {
        $count = count($this->sessions);

        Console::stdout("Sending $count redraws" . PHP_EOL);

        /* @var SocketSession $session */
        foreach ($this->sessions as $session) {
            Console::stdout("Sending redraw to " . $session->getToken());
            $session->sendRedraw($model, WSCommonHelper::STATUS_TEXT_REDRAW);
            Console::stdout(" Ok." . PHP_EOL);
        }
    }

    /**
     *
     */
    protected function sendRefresh()
    {
        $count = count($this->sessions);

        Console::stdout("Sending $count refreshes" . PHP_EOL);

        /* @var SocketSession $session */
        foreach ($this->sessions as $session) {
            Console::stdout("Sending refresh to " . $session->getToken());
            $session->sendRefresh(WSCommonHelper::STATUS_TEXT_REFRESH);
            Console::stdout("Ok." . PHP_EOL);
        }
    }
}
