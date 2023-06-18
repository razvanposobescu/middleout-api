<?php

namespace App\Http\Controllers;

use App\Models\JsonModel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Collection;

use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * We don't want to use the Response from laravel so this method should sufice :)
     *
     * @param Collection $json
     * @return string
     */
    protected function jsonResponse(Collection|JsonModel $json, int $responseCode = Response::HTTP_OK): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($responseCode);

        if ($json instanceof JsonModel)
        {
            $result = json_encode($json->toJson());
        }
        else
        {
            $result = $json->toJson();
        }

        echo $result;
        exit(0);
    }
}
