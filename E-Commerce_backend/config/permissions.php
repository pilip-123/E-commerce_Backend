<?php

return [
    'roles' => [
        'admin' => [
            '*',
        ],

        'manager' => [
            'VIEW_DASHBOARD',

            'VIEW_USERS',
            'CREATE_USERS',
            'UPDATE_USERS',

            'VIEW_PRODUCTS',
            'CREATE_PRODUCTS',
            'UPDATE_PRODUCTS',

            'VIEW_CATEGORIES',
            'CREATE_CATEGORIES',
            'UPDATE_CATEGORIES',

            'VIEW_SUPPLIERS',
            'CREATE_SUPPLIERS',
            'UPDATE_SUPPLIERS',

            'VIEW_CUSTOMERS',
            'CREATE_CUSTOMERS',
            'UPDATE_CUSTOMERS',

            'VIEW_ORDERS',
            'CREATE_ORDERS',
            'UPDATE_ORDERS',

            'VIEW_INVENTORY',
            'UPDATE_INVENTORY',

            'VIEW_REPORTS',
            'EXPORT_REPORTS',
        ],

        'staff' => [
            'VIEW_DASHBOARD',
            'VIEW_PRODUCTS',
            'VIEW_CUSTOMERS',
            'CREATE_CUSTOMERS',
            'VIEW_ORDERS',
            'CREATE_ORDERS',
            'VIEW_INVENTORY',
        ],

        'customer' => [
            'VIEW_PRODUCTS',
            'VIEW_ORDERS',
        ],
    ],
];
