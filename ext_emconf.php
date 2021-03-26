<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Extrameile Antivirus',
    'description' => 'TYPO3 Extension to check uploads against an anti virus tool like clamav or eset.',
    'category' => 'plugin',
    'author' => 'Alexander Opitz',
    'author_email' => 'opitz@extrameile-gehen.de',
    'author_company' => 'Extrameile GmbH',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'version' => '0.5.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-10.4.99',
            'php' => '7.0.8-7.4.99',
        ],
    ],
];
