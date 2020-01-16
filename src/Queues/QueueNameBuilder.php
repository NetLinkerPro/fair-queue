<?php


namespace NetLinker\FairQueue\Queues;


use NetLinker\FairQueue\Exceptions\FairQueueException;
use NetLinker\FairQueue\Models\ModelKey;

class QueueNameBuilder
{

    /**
     * Build queue name
     *
     * @param $queue
     * @param $modelId
     * @param string $modelKey
     * @return string
     * @throws FairQueueException
     */
    public static function build($queue, $modelKey, $modelId)
    {
        return 'fair_queue:' . $queue . ':' . $modelKey . ':' . $modelId;
    }
}