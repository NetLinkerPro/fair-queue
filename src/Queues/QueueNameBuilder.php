<?php


namespace NetLinker\FairQueue\Queues;


use Illuminate\Support\Str;
use NetLinker\FairQueue\Exceptions\FairQueueException;
use NetLinker\FairQueue\Models\ModelKey;

class QueueNameBuilder
{

    /**
     * Build queue name with model key
     *
     * @param $queue
     * @param $modelId
     * @param string $modelKey
     * @return string
     * @throws FairQueueException
     */
    public static function build($queue, $modelKey, $modelId)
    {
        $onlyQueueName = static::buildNameWithModelKey($modelKey, $queue);
        return 'fair_queue:' . $onlyQueueName . ':' . $modelId;
    }

    /**
     * Build only queue name
     *
     * @param $modelKey
     * @param string $queue
     * @return string
     */
    public static function buildNameWithModelKey($modelKey, $queue = 'default'){

        if (Str::endsWith($queue, ':' . $modelKey)){
            return $queue;
        }
        return $queue . ':' . $modelKey;
    }

    /**
     * Build only queue name
     *
     * @param $modelKey
     * @param string $queue
     * @return string
     */
    public static function buildOnlyName($modelKey, $queue){

        if (Str::endsWith($queue, ':' . $modelKey)){

            return Str::replaceLast(':' . $modelKey, '', $queue);
        }
        return $queue;
    }
}