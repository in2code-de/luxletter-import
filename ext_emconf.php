<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'luxletter import',
    'description' => 'Imports luxletter receivers to TYPO3 from a csv file',
    'category' => 'plugin',
    'version' => '2.0.0',
    'author' => 'Bastien Lutz',
    'author_email' => 'bastien.lutz@in2code.de',
    'author_company' => 'in2code.de',
    'state' => 'stable',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0',
            'luxletter' => '23.0.1',
        ],
        'conflicts' => [],
    ]
];
