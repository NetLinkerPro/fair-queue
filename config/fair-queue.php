<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Default models with identifier
     |
     | Model must contains ID column. Model selected from below `models` array.
     |--------------------------------------------------------------------------
     */
    'default_model' => 'user',

    /*
     |--------------------------------------------------------------------------
     | Models with identifier
     |
     | Models must contains ID column
     |--------------------------------------------------------------------------
     */
    'models' => [
        'user' => 'App\User',
    ]
];