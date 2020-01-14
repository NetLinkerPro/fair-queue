<?php


namespace Netlinker\FairQueue\Models;


class ModelSelect
{

    /**
     * Select
     *
     * @param null $modelKey
     * @return mixed
     */
    public static function select($modelKey){
        return config('fair-queue.models.' . $modelKey);
    }
}