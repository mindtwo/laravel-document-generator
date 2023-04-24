<?php

namespace mindtwo\DocumentGenerator\Document;

class Field
{
    public function __construct(
        public string $placeholder,
        public ?string $value,
    ) {
    }

    public function toArray(): array
    {
        return [
            $this->placeholder => $this->value,
        ];
    }
}
