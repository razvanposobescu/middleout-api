<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        /**
         * Render Only Custom Exceptions in Production
         */
        if ( App::environment('production'))
        {
            $this->renderable(function (Exception $e, Request $request): View|Response|null
            {
                if (is_a($e,\App\Exceptions\Exception::class))
                {
                    return response()->view('errors.production-error', [
                        'errorCode' => $e->getErrorCode(),
                        'message'   => $e->getMessage()
                    ], 200);
                }
            });
        }

    }
}
