<?php

namespace common\helpers;

use common\models\Apple;
use yii\db\Connection;

class AppleCommonHelper
{

    const MIN_GENERATED_COUNT = 10;
    const MAX_GENERATED_COUNT = 100;

    /**
     * @param int $minCount
     * @param int $maxCount
     *
     * @return bool
     * @throws \yii\base\Exception
     */
    public static function generateApples(int $minCount = AppleCommonHelper::MIN_GENERATED_COUNT, int $maxCount = AppleCommonHelper::MAX_GENERATED_COUNT)
    {
        $count = rand($minCount, $maxCount);

        try {
            $transaction = Apple::getDb()->beginTransaction();

            for ($i = 0; $i <= $count; $i++) {
                $model = new Apple();

                if (!$model->insert(false)) {
                    $transaction->rollBack();

                    return false;
                }
            }

            $transaction->commit();

        } catch (Exception $e) {
            throw $e;
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return true;

    }
}
