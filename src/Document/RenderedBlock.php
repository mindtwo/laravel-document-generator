<?php

namespace mindtwo\DocumentGenerator\Document;

class RenderedBlock
{
    public function __construct(
        public string $content,
        public array $fields,
    ) {
    }
}
