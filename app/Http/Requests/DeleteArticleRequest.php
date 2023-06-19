<?php

namespace App\Http\Requests;

use App\Enums\Errors\Codes;
use App\Exceptions\ValidationException;

/**
 * Since we're using laravel's Request lifecycle, we can extend them and add validations here
 * We're using our own custom validation rules that are basically the custom exceptions with error codes
 * Also in the Base Post Request we handle the Json response
 *
 * If we don't want to use Laravel's build in validator we can use the @method setValidator
 * to set our own custom validator.
 */
class DeleteArticleRequest extends BasePostRequest
{

    protected function prepareForValidation(): void
    {
        $this->merge(['id' => $this->route('article')]);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [

            'id' => [
                'required' => (new ValidationException(
                    errorCode: Codes::ARTICLE_INVALID_PARAM,
                    messageAttributes: ['article id is required!']
                ))->getMessage(),
                'exists' => (new ValidationException(
                    errorCode: Codes::ARTICLE_INVALID_PARAM,
                    messageAttributes: ['article not found!']
                ))->getMessage(),
            ]
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:articles,id',
        ];
    }
}
