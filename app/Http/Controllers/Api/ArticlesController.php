<?php

namespace App\Http\Controllers\Api;


use App\Enums\Errors\Codes;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Repositories\Article\ArticleRepository;
use \App\Exceptions\Exception as APIException;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticlesController extends Controller
{
    /**
     * TODO: Well since it's in the kernel of Laravel, we kinda need to use the request from the Service Container
     * TODO: unless we want to overwrite the whole request/response lifecycle. :)
     * @param Request $request
     *
     *
     * @param ArticleRepository $articleRepository
     */
    public function __construct(
        private readonly Request $request,
        private readonly ArticleRepository $articleRepository
    )
    {}

    /**
     * Get All Articles
     *
     * @return string
     */
    public function index(): string
    {
        try
        {
            // get all articles that are published
            // todo: we can parametrize the repo method, but i don't see any point for the test.
            $result = $this->articleRepository->all();

            // do some more cool stuff
            $responseCode = Response::HTTP_OK;

            // uncomment below exception to test generic error
            // throw new \ErrorException('test');
        }
        catch (APIException $exception)
        {
            $result = collect([
                'error' => $exception->getMessage(),
                'code' => $exception->getErrorCode()
            ]);

            $responseCode = Response::HTTP_OK;
        }
        catch (\Throwable)
        {
            // something happen somewhere somehow we just return a generic error
            // do some logging here and go into panic mode :)
            $result = collect([
                'error' => __('error_codes.'.Codes::GENERIC_ERROR->value),
                'code'  => Codes::GENERIC_ERROR->value
            ]);

            $responseCode = Response::HTTP_SERVICE_UNAVAILABLE;
        }

        return $this->jsonResponse($result, $responseCode);
    }

    /**
     * Get All Articles
     *
     * @return string
     */
    public function show(int $articleId = 0): string
    {
        try
        {
            // basic validation of the id since it's an unsigned integer make sure it's positive
            if ($articleId < 0)
            {
                // throw validation exception
                throw new ValidationException(errorCode: Codes::INVALID_ARGUMENT);
            }

            // get the article
            $result = $this->articleRepository->getById($articleId);


            // do some more cool stuff
            $responseCode = Response::HTTP_OK;

        }
        catch (APIException $exception)
        {
            $result = collect([
                'error' => $exception->getMessage(),
                'code' => $exception->getErrorCode()
            ]);

            $responseCode = Response::HTTP_OK;
        }
        catch (\Throwable)
        {
            // something happen somewhere somehow we just return a generic error
            // do some logging here and go into panic mode :)
            $result = collect([
                'error' => __('error_codes.'.Codes::GENERIC_ERROR->value),
                'code'  => Codes::GENERIC_ERROR->value
            ]);

            $responseCode = Response::HTTP_SERVICE_UNAVAILABLE;
        }

        return $this->jsonResponse($result, $responseCode);
    }



    private function testingShit()
    {


//    $repo = app(UserRepository::class);
//
//        /**
//         * @var UserRepository $user;
//         */
//        $userRepository = app(UserRepository::class);
//
//        /**
//         * @var \Illuminate\Database\Query\Builder $user1
//         */
//        $user1 = $userRepository->getById(9);
//
//
//        /**
//         * @var \Illuminate\Database\Query\Builder $user1
//         */
//        $user2 = $userRepository->getById(1);
//
////    $user3 = app(ArticleRepository::class)->getById(1);
//
//        $res = app(ArticleRepository::class)->getById(1);
//
//        dd($res);
//
//        die;
    }
}
