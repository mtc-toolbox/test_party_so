<?php


namespace console\controllers;

use console\daemons\CommandsServer;
use yii\console\Controller;

class ServerController  extends Controller
{
    public function actionStart()
    {
        $server = new CommandsServer();
        $server->start();
    }
}
