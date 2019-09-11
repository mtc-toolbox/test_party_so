<?php


namespace console\daemons;

use common\helpers\AppleCommonHelper;
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
    /**
     * @var \SplObjectStorage
     */
    protected $sessions;

    /**
     * CommandsServer constructor.
     */
    public function __construct()
    {
        $this->sessions = new \SplObjectStorage();

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
        $request = Json::decode($message);

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
        $session = $this->findSession($client);

        if (!isset($session)) {
            $session->sendState(WSCommonHelper::STATUS_DENIED, WSCommonHelper::STATUS_TEXT_DENIED);
            $session->close();
            $this->deleteSession($session);

            return;
        }

        if ($session->isGuest()) {
            $session->sendState(WSCommonHelper::STATUS_DENIED, WSCommonHelper::STATUS_TEXT_DENIED);
            $session->close();
            $this->deleteSession($session);

            return;
        }

        if (!AppleCommonHelper::generateApples()) {
            $session->sendState(WSCommonHelper::STATUS_UNKNOWN, WSCommonHelper::STATUS_TEXT_UNKNOWN);

            return;
        }

        /* @var SocketSession $session */
        foreach ($this->sessions as $session) {
            $session->sendRefresh(WSCommonHelper::STATUS_TEXT_REFRESH);
        }

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
}
