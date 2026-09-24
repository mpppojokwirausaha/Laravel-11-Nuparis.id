<?php

use App\Models\Activity;
use App\Models\Article;
use App\Models\Event;
use App\Models\Property;

return [
    'feeds' => [

        'article' => [
            'items' => [Article::class, 'getFeedItems'],
            'url' => '/article/feed',
            'title' => 'Artikel Terbaru - ' . config('app.name'),
            'description' => 'Kumpulan artikel terbaru dari ' . config('app.name'),
            'language' => 'id',
            'image' => '',
            'format' => 'atom',
            'view' => 'feed::atom',
            'type' => '',
            'contentType' => '',
        ],

        'activity' => [
            'items' => [Activity::class, 'getFeedItems'],
            'url' => '/activity/feed',
            'title' => 'Aktivitas  - ' . config('app.name'),
            'description' => 'Rangkaian aktivitas yang telah kami lakukan di ' . config('app.name'),
            'language' => 'id',
            'image' => '',
            'format' => 'atom',
            'view' => 'feed::atom',
            'type' => '',
            'contentType' => '',
        ],

        'event' => [
            'items' => [Event::class, 'getFeedItems'],
            'url' => '/event/feed',
            'title' => 'Event Terbaru - ' . config('app.name'),
            'description' => 'Kumpulan event dan kegiatan mendatang dari ' . config('app.name'),
            'language' => 'id',
            'image' => '',
            'format' => 'atom',
            'view' => 'feed::atom',
            'type' => '',
            'contentType' => '',
        ],

        'property' => [
            'items' => [Property::class, 'getFeedItems'],
            'url' => '/property/feed',
            'title' => 'Listing Properti Terbaru - ' . config('app.name'),
            'description' => 'Listing properti aktif dari ' . config('app.name'),
            'language' => 'id',
            'image' => '',
            'format' => 'atom',
            'view' => 'feed::atom',
            'type' => '',
            'contentType' => '',
        ],

    ],
];
