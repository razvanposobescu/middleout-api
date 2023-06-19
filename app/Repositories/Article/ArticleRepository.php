<?php

namespace App\Repositories\Article;

use App\Models\Article;
use App\Models\JsonModel;
use App\Enums\Errors\Codes;
use App\Repositories\Repository;
use App\Exceptions\ValidationException;
use App\Services\Cache\Attributes\CachedByProxy;

use App\Services\Cache\CacheService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Database\Connection;

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
     * @param bool $published
     * @return JsonModel|null
     * @throws ValidationException
     */
    #[CachedByProxy]
    public function getById(int $id, bool $published = true): ?JsonModel
    {
        try
        {
            $query = $this->newQuery()
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
                ->orderBy('article.published_at', 'DESC');

            // if published status is included add the where conditions
            if ($published)
            {
                $query->where('article.published_at', 'IS NOT', NULL);
            }

            // exec query
            $result = $query->get()->firstOrFail();

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

    /**
     * Get All articles
     *
     * @return JsonModel|null
     * @throws ValidationException
     */
    #[CachedByProxy]
    public function all(array $filters = []): ?Collection
    {
        try
        {
            $result = $this->newQuery()
                ->from(table: $this->model::getTable(), as: 'article')
                ->select( [
                    'article.id',
                    'article.user_id',
                    'article.title',
                    'article.body',
                    'article.published_at',
                    'user.id as user.id',
                    'user.email as user.email'
                ])
                ->join('users as user', 'article.user_id', '=', 'user.id')
                ->where(function (Builder $query) use ($filters)
                {
                    if ($filters['search'] !== null)
                    {
                        // TODO: I think we don't need to sanitize the search value for SQL Injection
                        // TODO: since the laravel query builder does that before biding? i might be wrong.
                        $term = $filters['search'];

                        // value
                        $query->where('article.title', 'like', "%$term%")
                            ->orWhere('article.body', 'like', "%$term%");
                    }

                })
                ->where('article.published_at', 'IS NOT', NULL)
                ->orderBy('article.published_at', 'DESC')
                ->get()
                ->all();

            // un dot the results
            $result = array_map(fn($item) => Arr::undot($item), $result);

            // since we joined the tables with the dot notion we can not undot the array
            // we can let the json mapper map the "User" key to a User Json Model :)
            return $this->mapData($result);
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

    /**
     * Create or update a resource
     *
     * @param array|Collection $resource
     * @return int|bool
     * @throws ValidationException
     */
    public function createOrUpdate(array|Collection $resource): int|bool
    {
        try
        {
            // cast collection to array
            $resource = $resource instanceof Collection ? $resource->toArray() : $resource;

            // get a new instance of the query builder
            $query = $this->newQuery();

            // get a cache Instance
            $cacheService = CacheService::make();

            // begin db transaction
            // since the laravel transaction are quite powerful
            // and it supports nested transactions pretty neat :)
            $result = $query->getConnection()->transaction(function (Connection $connection) use ($query, $resource, &$cacheService)
            {
                // let's check to see if we have a record matching the given id
                if (isset($resource['id']) && (int) $resource['id'] > 0)
                {
                    // get current record
                    $currentRecord = $this->getById((int) $resource['id']);

                    // check for differences
                    $fieldsToUpdate = array_diff_assoc($resource, $currentRecord->toArray());

                    if (count($fieldsToUpdate) > 0)
                    {
                        // cast published_at if we have it
                        if (isset($fieldsToUpdate['published_at']) && $fieldsToUpdate['published_at'] !== null)
                        {
                            $fieldsToUpdate['published_at'] = new \DateTime($fieldsToUpdate['published_at']);
                        }

                        $updateResult = $query
                            ->where(['id' => $currentRecord->id])
                            ->update($fieldsToUpdate);

                        // if update was successful return the current record id
                        if ($updateResult)
                        {
                            // let's clear the articles cache
                            $cacheService->flush(['articles']);

                            // and return the record id
                            return $currentRecord->id;
                        }
                        else
                        {
                            // if we don't have a successful update maybe rollback and throw an exception?
                            $connection->rollBack();
                        }
                    }

                    // record is the same just return the id
                    return $currentRecord->id;
                }

                // we should not do this here but hey for the sakes of the example we want to :)
                // if you want to see the transaction in action just comment out the filed
                // since the db filed is of date format, and we only accept EU format of dates
                // it should throw and violation exception on the db that the date is invalid
                $resource['published_at'] = (new \DateTime($resource['published_at']))
                    ->setTimestamp(time());

                // Insert new records or update the existing ones.
                $last_id = $query->insertGetId($resource);

                // commit everything until this point
                $connection->commit();

                // let's clear the articles cache
                CacheService::make()->flush(['articles']);

                // do some other db logic or selects or updates does not matter :)
                // if for example an exception is raised at this level everything that we executed in the transaction
                // until this point will be committed to the db do to the commit $connection->commit()
                // uncomment exception to see it in action xD

                //throw new \ErrorException('remove');

                return (int) $last_id;
            });

            // do some business logic with the exceptions from the transaction
            return match(true)
            {
                ($result instanceof Throwable) => throw $result,
                (is_int($result)), (is_bool($result)) => $result
            };
        }
        catch (Throwable $throwable)
        {
            throw new ValidationException(
                errorCode: Codes::GENERIC_SQL_ERROR,
                messageAttributes:[
                    $throwable->getMessage()
                ]
            );
        }
    }

    /**
     * @throws ValidationException
     */
    public function delete(int $id): bool
    {
        try
        {
            // get a new instance of the query builder
            $query = $this->newQuery();

            // get a cache Instance
            $cacheService = CacheService::make();

            // begin db transaction
            // since the laravel transaction are quite powerful
            // and it supports nested transactions pretty neat :)
            $result = $query->getConnection()->transaction(function (Connection $connection) use ($query, $id, &$cacheService)
            {
                /** @var Article $currentRecord */
                $currentRecord = $this->getById(id: $id, published: false);

                if ($currentRecord->isPublished())
                {
                    // soft delete the article
                    $result = $query
                        ->where(['id' => $currentRecord->id])
                        ->update(['published_at' => null]);
                }
                else
                {
                    // hard delete the record!
                    $result = $query->delete($currentRecord->id);
                }

                if ($result)
                {
                    $connection->commit();

                    // let's clear the articles cache
                    CacheService::make()->flush(['articles']);

                    return $result;
                }

                // cannot un publish or delete the resource!
                $connection->rollBack();

                throw new \ErrorException('Cannot Delete Resource!');
            });
            // do some business logic with the exceptions from the transaction
            return match(true)
            {
                ($result instanceof Throwable) => throw $result,
                (is_int($result)), (is_bool($result)) => $result
            };
        }
        catch (Throwable $throwable)
        {
            throw new ValidationException(
                errorCode: Codes::GENERIC_SQL_ERROR,
                messageAttributes:[
                    $throwable->getMessage()
                ]
            );
        }
    }
}
