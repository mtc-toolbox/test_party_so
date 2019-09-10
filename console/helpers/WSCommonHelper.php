<?php

namespace console\helpers;

use yii\helpers\Json;

/**
 * Class WSCommonHelper
 * @package console\helpers
 */
class WSCommonHelper
{
    const REFRESH_TEXT = 'Need to refresh page';

    const ACTION_REFRESH = 'refresh';
    const ACTION_STATE   = 'state';
    const ACTION_NONE    = '';

    const STATUS_OK      = 200;
    const STATUS_DENIED  = 403;
    const STATUS_UNKNOWN = 500;

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
        return static::buildMessage(static::STATUS_OK, $msg, static::ACTION_STATE);
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


        return $data;
    }
}
