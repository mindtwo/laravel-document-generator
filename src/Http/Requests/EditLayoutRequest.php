<?php

namespace mindtwo\DocumentGenerator\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class EditLayoutRequest extends FormRequest
{
    public function blocks(): Collection
    {
        return collect($this->validated('blocks', []));
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        if ($this->route()) {
            return $this->all() + $this->route()->parameters();
        }

        return parent::validationData();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'layoutIdentifier' => ['required', function ($attribute, $value, $fail) {
                if (! is_numeric($value)) {
                    return;
                }

                if (is_string($value) && \Ramsey\Uuid\Uuid::isValid($value)) {
                }

                $fail('The :attribute must be a valid document identifier.');
            }],
            'blocks' => 'required|array',
            'show_border' => 'boolean',
            'content_width' => 'string',
            'orientation' => 'string',
        ];
    }
}
