<?php
return [

    'app_id' => env('DIAGRO_APP_ID'),

    'app_name' => env('DIAGRO_APP_NAME'),

    'service_auth_uri' => env('DIAGRO_SERVICE_AUTH_URI'),

    /*'system' => [
        'frontend_application' => 8, //change this to the app id that is a system frontend application
        'default_user' => 'system@diagro.farm',

        'users' => [
            'system@diagro.farm' => 'eyJpdiI6IjdPLzI4L0dMUUhRaHhCRVhxcTYyeFE9PSIsInZhbHVlIjoiVnBBN0xmR2tITnM3UkNQdFdKWlVTZz09IiwibWFjIjoiOTA1Nzc0NTVmNmQ1MmNiNjRhMzQyYzI5MmY0ZDIwMzM2NmQ3MzM5MjI0OGVkZDkxNmI5ZWNlZDc4NWYyZjIwMCIsInRhZyI6IiJ9',
        ]
    ],*/

    /*
     * The rights the backend uses.
     */
    'rights' => [
        'rightName' => 'Right description',
        //...
    ],

    /*
     * Set name of the role or use '*' for permisson for the other roles.
     * Put name of the right and which permissions the role has for the right.
     * Set '*' for all other rights and the permissions.
     * Set '*' to give all permissions, this is equal to 'rcudpe'.
     */
    'roles' => [
        'roleName' => [
            'rightName' => 'rcudpe',
        ],
        'system' => [
            'company' => 'r',
            'user' => 'r',
            'role' => 'r',
        ],
        'root' => [
            '*' => '*'
        ],
        '*' => [
            'user' => 'ru',
            '*' => 'r'
        ],
    ],

];