<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'bkindler Form POST JSON Finisher',
    'description' => 'Provides HTTP JSON POST finisher for EXT:form',
    'category' => 'misc',
    'author' => 'Michael Manley; Björn Kindler',
    'author_email' => 'info@kindler-webservices.de',
    'state' => 'stable',
    'version' => '1.0.1',
    'constraints' => [
        'depends' =>
            [
                'typo3' => '11.5.0-13.4.99',
                'form' => '11.5.0-13.4.99',
            ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
