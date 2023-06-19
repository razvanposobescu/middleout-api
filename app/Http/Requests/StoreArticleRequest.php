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
class StoreArticleRequest extends BasePostRequest
{
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [

            'user_id' => [
                'required' => (new ValidationException(
                    errorCode: Codes::ARTICLE_INVALID_PARAM,
                    messageAttributes: ['article user_id is required!']
                ))->getMessage(),
                'exists' => (new ValidationException(
                    errorCode: Codes::ARTICLE_INVALID_PARAM,
                    messageAttributes: ['the article must have a valid user_id!']
                ))->getMessage(),
            ],
            'title' => [
                'required' => (new ValidationException(
                    errorCode: Codes::ARTICLE_INVALID_PARAM,
                    messageAttributes: ['article title is required!']
                ))->getMessage(),
            ],
            'body' => [
                'required' => (new ValidationException(
                    errorCode: Codes::ARTICLE_INVALID_PARAM,
                    messageAttributes: ['article body is required!']
                ))->getMessage(),
            ],
            'published_at' => [

                'required'=> (new ValidationException(
                    errorCode: Codes::ARTICLE_INVALID_PARAM,
                    messageAttributes: ['published_at filed is required']
                ))->getMessage(),

                'date_format' => (new ValidationException(
                    errorCode: Codes::ARTICLE_INVALID_PARAM,
                    messageAttributes: ['published_at should use the following format: d-m-Y H:i:s!']
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
            'user_id'       => 'required|exists:users,id',
            'title'         => 'required|max:200',
            'body'          => 'required|max:1000',
            'published_at'  => 'required|date_format:d-m-Y H:i:s',
        ];
    }
}
