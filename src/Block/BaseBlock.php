<?php

namespace mindtwo\DocumentGenerator\Block;

use mindtwo\DocumentGenerator\Models\DocumentBlock;

trait BaseBlock
{
    /**
     * {@inheritDoc}
     */
    public function position(): ?int
    {
        return $this->position > 1 ? $this->position : null;
    }

    /**
     * {@inheritDoc}
     */
    public function template(): string
    {
        return $this->template;
    }

    /**
     * {@inheritDoc}
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function show(): bool
    {
        return $this->show;
    }

    /**
     * {@inheritDoc}
     */
    public function render(?array $fields): string
    {
        if (! $this->show) {
            return '';
        }

        $rendered = $this->template();

        foreach ($fields as $field) {
            $placeholder = $field->placeholder;
            $value = $field->value;

            $rendered = str_replace("{{$placeholder}}", $value, $rendered);
        }

        return $rendered;
    }

    /**
     * {@inheritDoc}
     */
    public function hasEditor(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function hasTemplateFile(): bool
    {
        return false;
    }

    /**
     * Get array of columns we are allowed to update
     * You can also define an array $updateable in your class.
     *
     * @return array
     */
    public function updateable(): array
    {
        return $this->updateable ?? [];
    }

    /**
     * Transform template for saving in database.
     *
     * @return string
     */
    public function prepareTemplate(string $template): string
    {
        return $template;
    }

    /**
     * Get array of all placeholders we need to resolve.
     *
     * @return array
     */
    public function placeholder(): array
    {
        $placeholders = [];
        preg_match_all('/{(?<placeholder>[a-z\_]*)}/m', $this->template(), $placeholders);

        return $placeholders['placeholder'] ?? [];
    }

    public static function from(DocumentBlock $documentBlock): self
    {
        return new self(
            $documentBlock->name,
            $documentBlock->template,
            $documentBlock->show,
            $documentBlock->position,
        );
    }

    public static function make(string $name, string $template, bool $show = true, int $position = -1): self
    {
        return new self(
            $name,
            $template,
            $show,
            $position,
        );
    }
}
