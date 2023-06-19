<?php

namespace App\Http\Requests;

use Symfony\Component\HttpFoundation\Response;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

/**
 * Just Some basic Functionality for request validation
 *
 */
abstract class BasePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // here we can validate if the user is allowed to do the request
        // have some business logic, etc
        return true;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return JsonResponse
     */
    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new HttpResponseException(
            new JsonResponse(
                data: $validator->getMessageBag(),
                status: Response::HTTP_BAD_REQUEST
            )
        );
    }
}
