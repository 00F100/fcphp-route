<?php

return [
    'v1/users' => [
        [
            'action' => 'Controller@getAll',
            'view' => [
                'theme' => 'default',
                'layout' => 'users.list',
            ],
        ],
        [
            'method' => 'GET',
            'route' => '{parentId}',
            'action' => 'Controller@getByParent',
            
            // 'thread' => false,
            'view' => 'json',

            'filter' => [
                'default' => 'escape',
                'body' => [
                    'type' => 'xml',
                    'content' => 'xmlparser'
                ],
                'query' => [
                    'name' => 'raw',
                ]
            ]
        ],
        [
            'method' => 'POST',
            'rule' => 'create-user',
            'action' => 'Controller@create',
            'filter' => [
                'default' => 'escape',
                'body' => [
                    'type' => 'json',
                    'content' => [
                        'name' => 'raw',
                    ]
                ]
            ]
        ]
    ],
    'v2/person' => [
        [
            'method' => 'GET',
            'route' => '{parentId}',
            'action' => 'Controller@getByParent',
            'filter' => [
                'default' => 'escape',
                'query' => [
                    'name' => 'raw',
                ]
            ]
        ],
    ]
];
