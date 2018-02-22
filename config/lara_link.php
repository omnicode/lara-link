<?php
return [
    'default' => [
        'btn' => 'primary'
    ],
    'general' => [
        'icon' => '',
        'replace' => [
            'symbols' => [
                '_' => '-',
            ],
        ],
        'confirmation' => [
            'class' => 'ml5',
            'message' => 'Are you sure you want to :action this :item?'
        ]
    ],
    'actions' => [
        'show' => [
            'icon' => 'eye',
            'title' => 'View Details'
        ],
        'edit' => [
            'icon' => 'pencil'
        ],
        'destroy' => [
            'icon' => 'trash',
            'confirmation' => 'Are you sure you want to delete this :item?'

        ],
    ]
];
