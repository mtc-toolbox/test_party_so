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
        $session = $this->findSession($client);

        if (!isset($session)) {
            Console::stdout("Session not found. Create new." . PHP_EOL);
            $session->sendState(WSCommonHelper::STATUS_DENIED, WSCommonHelper::STATUS_TEXT_DENIED);
            $session->close();
            $this->deleteSession($session);

            return;
        }

        Console::stdout("Getting token" . PHP_EOL);

        $session->setData($msg);

        Console::stdout("Check user" . PHP_EOL);

        if ($session->isGuest()) {
            Console::stdout("Session is for guest" . PHP_EOL);
            $session->sendState(WSCommonHelper::STATUS_DENIED, WSCommonHelper::STATUS_TEXT_DENIED);
            $session->close();
            $this->deleteSession($session);

            return;
        }
        Console::stdout("Create apples" . PHP_EOL);

        if (!AppleCommonHelper::generateApples()) {
            Console::stdout("Session could not generate apples" . PHP_EOL);
            $session->sendState(WSCommonHelper::STATUS_UNKNOWN, WSCommonHelper::STATUS_TEXT_UNKNOWN);

            return;
        }

        $count = count($this->sessions);

        Console::stdout("Sending $count refreshes" . PHP_EOL);

        /* @var SocketSession $session */
        foreach ($this->sessions as $session) {
            Console::stdout("Sending refresh to " . $session->getToken());
            $session->sendRefresh(WSCommonHelper::STATUS_TEXT_REFRESH);
            Console::stdout("Ok." . PHP_EOL);
        }
        $client->send('good');

        Console::stdout("Connection done command generate" . PHP_EOL);

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
