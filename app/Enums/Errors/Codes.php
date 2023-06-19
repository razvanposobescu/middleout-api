<?php

namespace App\Enums\Errors;

/**
 * Error Codes For MiddleoutAPI
 *
 * TODO: Why have error codes in the first place? - Well it will help us pint point exactly where an issue may occur
 *       Also, we can implement different business logic if an error code is thrown for example
         1000 to 2000 - should be generic error codes. used all over the app for misc things
         2001 to 3000 - should be for example: own repos
 */
enum Codes: int
{
    /**
     * Implementation of Generic Error Codes
     */
    case GENERIC_ERROR = 1000
    ;
    case GENERIC_SQL_ERROR = 1500;

    case INVALID_INSTANCE = 1001;

    case INVALID_ARGUMENT = 1002;

    case RESOURCE_NOT_FOUND = 1003;

    /**
     * Validation error Codes
     *
     */
    case ARTICLE_INVALID_PARAM = 1004;
}
