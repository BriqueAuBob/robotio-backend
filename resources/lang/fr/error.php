<?php
return [
    '401' => [
        'message' => 'Vous devez vous connecter pour effectuer cette requête.',
        'bad_access_token' => 'Assurez vous que votre "access token" sois valide',
    ],

    '403' => [
        'message' => 'Vous n\'avez pas les permissions pour accèder à cette ressource...',
    ],

    '404' => [
        'message' => 'Nous avons pas trouvé de résultat correspondant à votre recherche...',
        'could_not_match_tokens' => 'We couldn\'t find any tokens that match to provided data.',
    ],

    '405' => [
        'message' => 'Cette méthode n\'est pas autorisée sur cette route.',
    ],

    '500' => [
        'message' => 'Le serveur a rencontré un problème.',
    ],

    '503' => [
        'message' => 'Service Unavailable',
        'cannot_identify_discord_access_token' => 'Provided access token is incorrect',
    ]
];
