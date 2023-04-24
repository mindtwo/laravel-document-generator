<?php

return [

    /**
     * Path where the document migrations are located
     *
     * Default: 'database_path('documents')'
     */
    'migrations_path' => database_path('documents'),

    /**
     * File related configuration
     */
    'files' => [

        /**
         * Disk to save documents in.
         *
         * Default: 'env('DOCUMENT_DISK', 'local')'
         */
        'disk' => env('DOCUMENT_DISK', 'local'),

        /**
         * Default file name generator class to use to retrieve document names.
         * If null use 'date_modelType_layoutName.pdf'
         *
         * Default: null
         */
        'name_generator' => null,

        /**
         * Path in disk for generated documents.
         *
         * Default: 'documents'
         */
        'path' => 'documents',

        /**
         * Tmp path for temp save of generated files.
         *
         * Default: '/tmp/documents'
         */
        'tmp' => '/tmp/documents',
    ],

    /**
     * Security related configuration
     */
    'security' => [

        /**
         * Policy class extending mindtwo\DocumentGenerator\Security\DocumentPolicy.
         *
         * Default: 'null'
         */
        'policy' => null,

    ],

    /**
     * Placeholder config options
     */
    'placeholder' => [

        /**
         * Auto discover paths for Placeholder
         * classes without leading slash
         *
         * Default: 'Documents/Placeholders'
         */
        'auto_discover' => [
            'Documents/Placeholders',
        ],

    ],

    /**
     * Document block template related
     */
    'templates' => [

        /**
         * Template roots used to discover
         * templates for our blocks
         *
         * Default: 'resource_path('views/documents')'
         */
        'roots' => [
            resource_path('views/documents'),
        ],
    ],
];
