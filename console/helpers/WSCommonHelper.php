<?php

namespace console\helpers;

use common\models\Apple;
use yii\helpers\Json;

/**
 * Class WSCommonHelper
 * @package console\helpers
 */
class WSCommonHelper
{
    const REFRESH_TEXT = 'Need to refresh page';

    const ACTION_REFRESH = 'refresh';
    const ACTION_REDRAW  = 'redraw';
    const ACTION_STATE   = 'state';
    const ACTION_NONE    = '';

    const STATUS_OK      = 200;
    const STATUS_DENIED  = 403;
    const STATUS_UNKNOWN = 500;

    const STATUS_TEXT_OK      = 'Ok';
    const STATUS_TEXT_REFRESH = 'Need to refresh browser';
    const STATUS_TEXT_REDRAW  = 'Need to redraw item';
    const STATUS_TEXT_DENIED  = 'Access denied';
    const STATUS_TEXT_UNKNOWN = 'Unknown action error';

    /**
     * @return array|string
     */
    public static function buildRefreshMessage()
    {
        return static::buildMessage(static::STATUS_OK, static::REFRESH_TEXT, static::ACTION_REFRESH);
    }

    /**
     * @return array|string
     */
    public static function buildStateMessage(int $state = WSCommonHelper::STATUS_OK, string $msg = '')
    {
        return static::buildMessage($state, $msg, static::ACTION_STATE);
    }

    public static function buildRedrawMessage(Apple $model, string $message = '')
    {
        $data = [
            'id' => $model->Id,
            'eated' => $model->IntegrityPercent.'%',
            'message' => $model->getStateName(),
            'ttb' => $model->getTimeToBad(),
            'state' => $model->getState(),
        ];

        return static::buildMessage(
            static::STATUS_OK,
            static::STATUS_TEXT_REDRAW,
            static::ACTION_REDRAW,
            $data
            );
    }

    /**
     * @param int         $state
     * @param string      $msg
     * @param string|null $action
     * @param array|null  $data
     *
     * @return array|string
     */
    public static function buildMessage(int $state = WSCommonHelper::STATUS_OK, string $msg = '', string $action = null, array $data = null)
    {
        $result = [
            'state'  => [
                'code'    => $state,
                'message' => $msg,
            ],
            'action' => $action ?? static::ACTION_NONE,
            'data'   => $data ?? [],
        ];

        return Json::encode($result);
    }
}
