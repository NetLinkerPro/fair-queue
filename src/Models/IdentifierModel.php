<?php


namespace Netlinker\FairQueue\Models;

use Illuminate\Support\Facades\Cache;

class IdentifierModel
{
    /**
     * Max ID
     *
     * @param string|null $modelKey
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function maxId(string $modelKey)
    {

        $cacheKey = 'fair-queue:' . $modelKey . ':max-id';

        $maxId = Cache::store('redis')->get($cacheKey);

        if (!$maxId) {
            $model = ModelSelect::select($modelKey);
            $maxId = $model::max('id');
            Cache::store('redis')->put($cacheKey, $maxId, 1);
        }

        return $maxId;

    }

}