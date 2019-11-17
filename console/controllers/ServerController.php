<?php

namespace console\controllers;

use console\daemons\CommandsServer;
use yii\console\Controller;

/**
 * Class ServerController
 * @package console\controllers
 */
class ServerController  extends Controller
{
    /**
     * yii server/start
     */
    public function actionStart()
    {
        $server = new CommandsServer();
        $server->start();
    }
}
