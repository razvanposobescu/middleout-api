<?php

namespace App\Repositories\Article;

use App\Enums\Errors\Codes;
use App\Repositories\Repository;
use App\Exceptions\ValidationException;

use App\Models\Article;
use App\Models\JsonModel;

use App\Services\Cache\Attributes\CachedByProxy;
use Illuminate\Support\Arr;
use Throwable;

/**
 * Article Repository
 * @note: if you want to proxy the repo don't make the class final
 */
class ArticleRepository extends Repository implements ArticleRepositoryInterface
{
    /**
     * Set Repository Model
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = app(Article::class);
    }

    /**
     * Get Article By ID
     *
     * @param int $id
     * @return JsonModel|null
     * @throws ValidationException
     */
    #[CachedByProxy]
    public function getById(int $id): ?JsonModel
    {
        try
        {
            $result = $this->newQuery()
                ->from(table: $this->model::getTable(), as: 'article')
                ->select([
                    'article.id',
                    'article.user_id',
                    'article.title',
                    'article.body',
                    'article.published_at',
                    'user.id as user.id',
                    'user.email as user.email'
                ])
                ->join('users as user', 'article.user_id', '=', 'user.id')
                ->where(['article.id' => $id])
                ->get()
                ->firstOrFail();

            // since we joined the tables with the dot notion we can not undot the array
            // we can let the json mapper map the "User" key to a User Json Model :)
            return $this->mapData((object) Arr::undot($result));
        }
        catch (Throwable $throwable)
        {
            throw new ValidationException(
                errorCode: Codes::RESOURCE_NOT_FOUND,
                messageAttributes:[
                    $throwable->getMessage()
                ]
            );
        }
    }
}
