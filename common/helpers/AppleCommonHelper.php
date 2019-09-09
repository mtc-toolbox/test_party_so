<?php

namespace common\helpers;

class AppleCommonHelper
{

    const MIN_GENERATED_COUNT = 10;
    const MAX_GENERATED_COUNT = 100;

    public static function generateApples(int $minCount = AppleCommonHelper::MIN_GENERATED_COUNT, int $maxCount = AppleCommonHelper::MAX_GENERATED_COUNT)
    {
        $models = [];

        $count = rand($minCount, $maxCount);

        for ($i = 0; $i < $count; $i++) {
        }

    }
}
