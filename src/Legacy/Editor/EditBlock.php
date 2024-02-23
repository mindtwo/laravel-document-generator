<?php

namespace mindtwo\DocumentGenerator\Editor;

class EditBlock
{
    public function __construct(
        public string $name,
        public string $content,
        public bool $show,
        public bool $hasEditor,
        public array $fields,
        public array $fieldNames,
        public string $rawContent,
    ) {
    }
}
