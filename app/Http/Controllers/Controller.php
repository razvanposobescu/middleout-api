<?php

namespace App\Http\Controllers;

use App\Models\JsonModel;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;


class Controller extends BaseController
{
    // default traits, do we need them?
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Do some more basic logic before returning the response if needed.
     *
     * @param Collection|JsonModel $responseBody
     * @param int $responseCode
     * @return JsonResponse
     */
    protected function jsonResponse(Collection|JsonModel $responseBody, int $responseCode = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(data: $responseBody, status: $responseCode);
    }
}
