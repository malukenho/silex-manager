<?php

// TODO: move config to readme.md
// NOTE: name of the action. Can be (index, new, edit, edit)
return [

    // List action
    'index' => [
        'columns' => [
            'id'    => 'Id',
            'name'  => 'Name',
            'email' => 'Email',
        ],
        'header' => 'Manager users',
        'icon'   => 'user',
        'pagination' => true,
        'item_per_page' => 10,
        'action' => [
            'new' => 'Create a new user',
            'edit' => 'Edit',
            'delete' => 'Delete',
        ],
        'search' => [
            'input' =>[
                [
                    'name' => 'name',
                    'placeholder' => 'Find by name',
                ],
            ],
            'button' => 'Filtrar'
        ],
    ],

     // New action configuration
    'new'    => [
        'columns' => [
            'name'     => [],
            'email'    => [
                'type' => 'email',
            ],
        ],

        'before' => function (array $data) {
            mail($data['email'], 'You was registered', 'Great! You was registered.');
        },
        'header'  => 'New user',
        'pk'      => 'id',
        'icon'    => 'user',
    ],

    // Edit can use same config from `new` or use your `own`
    'edit'   => [
        'columns' => [
            'use_new_form' => true,
        ],
        'header'  => 'Edit a user',
        'pk'      => 'id',
        'icon'    => 'user',
    ],

    'delete' => [
        'pk' => 'id',
    ],
];
