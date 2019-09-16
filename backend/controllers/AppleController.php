<?php

namespace backend\controllers;

use console\models\SocketSession;
use Yii;
use common\models\Apple;
use common\models\AppleSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AppleController implements the CRUD actions for Apple model.
 */
class AppleController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Apple models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new AppleSearch();
        $dataProvider = $searchModel->search();

        $token = Yii::$app->request->getCsrfToken(false);

        $key = Yii::$app->cache->buildKey($token);

        if (Yii::$app->cache->exists($key)) {
            Yii::$app->cache->delete($key);
        }
        Yii::$app->cache->add($key, Yii::$app->user->id);

        return $this->render('@app/views/apple/index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
