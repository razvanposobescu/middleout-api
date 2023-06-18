<?php

use App\Enums\Errors\Codes as ErrorCode;

return [

    /*
    |--------------------------------------------------------------------------
    | Error Codes Messages
    |--------------------------------------------------------------------------
    |
    | The following lines contain the default error messages used by the Website.
    | Each Error Code has its own message. Feel free to tweak each of these messages here.
    |
    */
    ErrorCode::GENERIC_ERROR->value => "Something went wrong, please try again later.",
    ErrorCode::INVALID_INSTANCE->value => "%s must be an instance of %s!",
    ErrorCode::RESOURCE_NOT_FOUND->value => "%s not found!",
    ErrorCode::INVALID_ARGUMENT->value => "Invalid argument(s)!",
];
