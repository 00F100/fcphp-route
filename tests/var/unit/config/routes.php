<?php

return [
    'v1/users' => [
        [
            'action' => 'Controller@getAll',
        ],
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
        [
            'method' => 'POST',
            'rule' => 'create-user',
            'action' => 'Controller@create',
            'filter' => [
                'default' => 'escape',
                'body' => [
                    'name' => 'raw',
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