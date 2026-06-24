<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Search Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default search connection that will be used to
    | index and search your models. This connection will be used by default
    | unless another connection is explicitly specified when searching.
    |
    | Supported: "algolia", "meilisearch", "typesense", "database", "collection", "null"
    |
    */

    'driver' => env('SCOUT_DRIVER', 'algolia'),

    /*
    |--------------------------------------------------------------------------
    | Index Prefix
    |--------------------------------------------------------------------------
    |
    | Here you may specify a prefix that will be applied to all model index
    | names before they are sent to the search engine. This is useful if
    | you share one search engine instance across multiple applications.
    |
    */

    'prefix' => env('SCOUT_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Queue Data Syncing
    |--------------------------------------------------------------------------
    |
    | This option allows you to control if the operations that synchronize
    | your model data with your search engines are queued. When set to
    | true, all automatic operations will be queued for performance.
    |
    */

    'queue' => env('SCOUT_QUEUE', true),

    /*
    |--------------------------------------------------------------------------
    | Chunk Sizes
    |--------------------------------------------------------------------------
    |
    | These options control the maximum number of records that will be sent
    | to your search engine in a single batch. Selecting a larger chunk
    | size will perform fewer queries but will require more memory.
    |
    */

    'chunk' => [
        'searchable' => 500,
        'unsearchable' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Soft Deletes
    |--------------------------------------------------------------------------
    |
    | This option allows you to control if soft deleted models should be
    | excluded from the search index. When set to true, soft deleted
    | models will be removed from the index when they are deleted.
    |
    */

    'soft_delete' => false,

    /*
    |--------------------------------------------------------------------------
    | Identify User
    |--------------------------------------------------------------------------
    |
    | This option allows you to control if the authenticated user's ID
    | will be sent to the search engine with search queries. This is
    | useful for search engines that support user personalization.
    |
    */

    'identify' => env('SCOUT_IDENTIFY', false),

    /*
    |--------------------------------------------------------------------------
    | Algolia Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Algolia settings. Algolia is a hosted
    | search engine that provides dynamic search indexing and query
    | capabilities. You will need an Algolia account to use this.
    |
    */

    'algolia' => [
        'id' => env('ALGOLIA_APP_ID', ''),
        'secret' => env('ALGOLIA_SECRET', ''),
    ],

];
