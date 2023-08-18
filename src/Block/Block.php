<?php

namespace mindtwo\DocumentGenerator\Block;

use mindtwo\DocumentGenerator\Enums\DocumentOrientation;

interface Block
{
    /**
     * Get our template string for this block
     *
     * @return string
     */
    public function template(): string;

    /**
     * Get blocks position in document
     *
     * @return bool
     */
    public function position(): ?int;

    /**
     * Check if block is shown in document
     *
     * @return bool
     */
    public function show(): bool;

    /**
     * Check if block can be edited
     *
     * @return bool
     */
    public function hasEditor(): bool;

    /**
     * Get rendered blocks that are substituted
     *
     * @param  array  $fields
     * @param  DocumentOrientation  $orientation
     * @return string
     */
    public function render(array $fields, DocumentOrientation $orientation): string;

    /**
     * Get array of columns we are allowed to update
     *
     * @return array
     */
    public function updateable(): array;

    /**
     * Transform template for saving in database
     *
     * @return string
     */
    public function prepareTemplate(string $template): string;

    /**
     * Check if this block has a template file which we want
     * to use to define the block in rendered document
     *
     * @return bool
     */
    public function hasTemplateFile(): bool;

    /**
     * Get array of all placeholders we need to resolve
     *
     * @return array
     */
    public function placeholder(): array;

    /**
     * Get unrendered block template
     *
     * @return string
     */
    public function unrender(): string;
}
