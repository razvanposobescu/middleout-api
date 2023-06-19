<?php

namespace App\Exceptions;

use App\Enums\Errors\Codes as ErrorCode;

use Illuminate\Support\Facades\Lang;
use Throwable;

/**
 * Handle Exceptions
 */
abstract class Exception extends \Exception implements Throwable
{
    /**
     * Error Code
     *
     * @param ErrorCode $errorCode
     * @param array $messageAttributes
     */
    public function __construct(private readonly ErrorCode $errorCode, private readonly array $messageAttributes = [])
    {
        parent::__construct(
            $this->getErrorMessage(),
            $this->getErrorCode()
        );
    }

    /**
     * Get Error Code
     *
     * @return int
     */
    public function getErrorCode(): int
    {
        $this->code = $this->errorCode->value;
        return $this->code;
    }

    /**
     * Get Error Message
     *
     * @return string
     */
    private function getErrorMessage(): string
    {
        return sprintf(
            '[ERROR][%s] - %s',
            $this->errorCode->value,
            vsprintf(
                Lang::get(key: "error_codes.{$this->errorCode->value}"),
                $this->messageAttributes
            )
        );
    }
}
