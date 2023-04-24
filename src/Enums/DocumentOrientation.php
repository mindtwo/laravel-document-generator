<?php

namespace mindtwo\DocumentGenerator\Enums;

enum DocumentOrientation: string
{
    case Landscape = 'landscape';
    case Portrait = 'portrait';

    public function dimensions(): array
    {
        return match ($this) {
            DocumentOrientation::Landscape => ['width' => 29.7, 'height' => 21],
            DocumentOrientation::Portrait => ['width' => 21, 'height' => 29.7],
        };
    }
}
