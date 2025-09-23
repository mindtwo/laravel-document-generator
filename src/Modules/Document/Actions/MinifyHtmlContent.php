<?php

namespace mindtwo\DocumentGenerator\Modules\Document\Actions;

use voku\helper\HtmlMin;

class MinifyHtmlContent
{
    /**
     * Minify the given HTML content.
     *
     * @param  string|null  $content  The HTML content to minify.
     * @return string|null The minified HTML content, or null if the input was null.
     */
    public function __invoke(?string $content): ?string
    {
        if (is_null($content)) {
            return null;
        }

        // Initialize the HTML minifier
        $min = new HtmlMin;

        return $min->minify($content);
    }
}
