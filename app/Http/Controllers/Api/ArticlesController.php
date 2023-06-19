<?php

namespace App\Http\Controllers\Api;


use App\Enums\Errors\Codes;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteArticleRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Repositories\Article\ArticleRepository;
use \App\Exceptions\Exception as APIException;

use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticlesController extends Controller
{
    /**
     * TODO: Well since it's in the kernel of Laravel, we kinda need to use the request from the Service Container
     * TODO: unless we want to overwrite the whole request/response lifecycle. :)
     *
     * TODO: we can inject the request object inside the constructor and use it in all methods,
     * TODO: or we can inject it, per method
     *
     * TODO: Since most of the CRUD Methods have more or less the same try catch block maybe wrap them in a closure?
     *
     * @param ArticleRepository $articleRepository
     */
    public function __construct(
        private readonly ArticleRepository $articleRepository
    )
    {}

    /**
     * Get All Articles
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try
        {
            // maybe sanitize the input?
            $search = $request->get('search') ?? null;
            $search = strip_tags($search);

            // get all articles that are published
            // todo: we can parametrize the repo method, but i don't see any point for the test.
            $articles = $this->articleRepository->all(['search' => $search]);

            // do some logic, etc

            // build response
            $response = [
                'responseBody' => $articles,
                'responseCode' => Response::HTTP_OK,
            ];

            // uncomment below exception to test generic error
            // throw new \ErrorException('test');
        }
        catch (APIException $exception)
        {
            $response = [
                'responseBody' => collect([
                    'error' => $exception->getMessage(),
                    'code' => $exception->getErrorCode()
                ]),
                'responseCode' => Response::HTTP_OK,
            ];
        }
        catch (\Throwable)
        {
            // something happen somewhere somehow we just return a generic error
            // do some logging here and go into panic mode :)
            $response = [
                'responseBody' => collect([
                    'error' => __('error_codes.'.Codes::GENERIC_ERROR->value),
                    'code'  => Codes::GENERIC_ERROR->value
                ]),
                'responseCode' => Response::HTTP_SERVICE_UNAVAILABLE,
            ];
        }

        return $this->jsonResponse(...$response);
    }

    /**
     * Get All Articles
     *
     * @param int $articleId
     * @return JsonResponse
     */
    public function show(int $articleId = 0): JsonResponse
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
            $article = $this->articleRepository->getById($articleId);


            // do some more cool stuff


            // build response
            $response = [
                'responseBody' => $article,
                'responseCode' => Response::HTTP_OK,
            ];
        }
        catch (APIException $exception)
        {
            $response = [
                'responseBody' => collect([
                    'error' => $exception->getMessage(),
                    'code' => $exception->getErrorCode()
                ]),
                'responseCode' => Response::HTTP_BAD_REQUEST,
            ];
        }
        catch (\Throwable)
        {
            // something happen somewhere somehow we just return a generic error
            // do some logging here and go into panic mode :)

            $response = [
                'responseBody' => collect([
                    'error' => __('error_codes.'.Codes::GENERIC_ERROR->value),
                    'code'  => Codes::GENERIC_ERROR->value
                ]),
                'responseCode' => Response::HTTP_SERVICE_UNAVAILABLE,
            ];
        }

        return $this->jsonResponse(...$response);
    }

    /**
     * Create a new Article
     *
     * @param StoreArticleRequest $request
     * @return JsonResponse
     */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        return $this->updateOrCrate($request);
    }

    /**
     * Update an Article
     *
     * @param UpdateArticleRequest $request
     * @return JsonResponse
     */
    public function update(UpdateArticleRequest $request): JsonResponse
    {
        return $this->updateOrCrate($request);
    }

    public function destroy(DeleteArticleRequest $request): JsonResponse
    {
        try
        {
            // delete given article
            $deleted = $this->articleRepository->delete($request->validated()['id']);

            $response = [
                'responseBody' => collect([
                    'result' => $deleted,
                ]),
                'responseCode' => Response::HTTP_CREATED
            ];
        }
        catch (APIException $exception)
        {
            $response = [
                'responseBody' => collect([
                    'error' => $exception->getMessage(),
                    'code' => $exception->getErrorCode()
                ]),
                'responseCode' => Response::HTTP_BAD_REQUEST,
            ];
        }
        catch (\Throwable)
        {
            // something happen somewhere somehow we just return a generic error
            // do some logging here and go into panic mode :)
            $response = [
                'responseBody' => collect([
                    'error' => __('error_codes.' . Codes::GENERIC_ERROR->value),
                    'code' => Codes::GENERIC_ERROR->value
                ]),
                'responseCode' => Response::HTTP_SERVICE_UNAVAILABLE,
            ];
        }

        return $this->jsonResponse(...$response);
    }

    /**
     * @param UpdateArticleRequest $request
     * @return JsonResponse
     */
    private function updateOrCrate(Request $request): JsonResponse
    {
        try
        {
            // create new article
            $articleId = $this->articleRepository->createOrUpdate($request->validated());

            $response = [
                'responseBody' => collect([
                    'id' => $articleId,
                ]),
                'responseCode' => Response::HTTP_CREATED
            ];
        }
        catch (APIException $exception)
        {
            $response = [
                'responseBody' => collect([
                    'error' => $exception->getMessage(),
                    'code' => $exception->getErrorCode()
                ]),
                'responseCode' => Response::HTTP_BAD_REQUEST,
            ];
        }
        catch (\Throwable)
        {
            // something happen somewhere somehow we just return a generic error
            // do some logging here and go into panic mode :)

            $response = [
                'responseBody' => collect([
                    'error' => __('error_codes.' . Codes::GENERIC_ERROR->value),
                    'code' => Codes::GENERIC_ERROR->value
                ]),
                'responseCode' => Response::HTTP_SERVICE_UNAVAILABLE,
            ];
        }

        return $this->jsonResponse(...$response);
    }
}
