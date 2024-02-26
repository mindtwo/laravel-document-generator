<?php

return [

    /**
     * Default document generator class used to generate the document.
     */
    'file_creator' => \mindtwo\DocumentGenerator\Modules\Generation\Services\DomPdfFileCreator::class,

    /**
     * File related configuration
     */
    'files' => [

        /**
         * Path in disk for generated documents.
         *
         * Default: 'documents'
         */
        'root_path' => 'documents',

        /**
         * Disk to save documents in.
         *
         * Default: 'env('DOCUMENT_DISK', 'local')'
         */
        'default_disk' => env('DOCUMENT_DISK', 'local'),

        /**
         * Default file name generator class used to get the name for the document.
         *
         * Default: \mindtwo\DocumentGenerator\Modules\Generation\DefaultFileNameGenerator::class
         */
        'file_name_generator' => \mindtwo\DocumentGenerator\Modules\Generation\DefaultFileNameGenerator::class,

        /**
         * Default file path generator used to get the path for the document relative to root.
         *
         * Default: null
         */
        'file_path_generator' => \mindtwo\DocumentGenerator\Modules\Generation\DefaultFilePathGenerator::class,

        /**
         * Tmp path for temp save of generated files.
         *
         * Default: '/tmp/documents'
         */
        'tmp_path' => '/tmp/documents',
    ],

    /**
     * Placeholder config options
     */
    'placeholder' => [

        /**
         * Auto discover paths for Placeholder
         * classes without leading slash
         *
         * Default: 'Documents/Placeholder'
         */
        'auto_discover' => [
            'Documents/Placeholder',
        ],

    ],
];
