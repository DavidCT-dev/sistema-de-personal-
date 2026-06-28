<?php

return [
    'exports' => [
        'storage_path' => 'app/exports',
    ],
    'imports' => [
        'read_only' => true,
    ],
    'extension_detector' => [
        'xlsx' => 'Xlsx',
        'xls'  => 'Xls',
        'csv'  => 'Csv',
    ],
];