<?php
return [
    '401' => [
        'message' => 'You need to login to make the request.',
        'bad_access_token' => 'You\'re access_token is invalid.',
    ],

    '403' => [
        'message' => 'You don\'t have the permission to access this resource.',
    ],

    '404' => [
        'message' => 'We can\'t find any result for your search...',
        'could_not_match_tokens' => 'We couldn\'t find any tokens that match to provided data.',
    ],

    '405' => [
        'message' => 'This method isn\'t authorised on this route.',
    ],

    '500' => [
        'message' => 'The server meet a problem.',
    ],

    '503' => [
        'message' => 'Service Unavailable',
        'cannot_identify_discord_access_token' => 'Provided access token is incorrect',
    ]
];
