<?php

namespace App\Repositories\User;

use App\Enums\Errors\Codes;
use App\Exceptions\ValidationException;
use App\Models\JsonModel;
use App\Models\User;
use App\Repositories\Repository;
use App\Services\Cache\Attributes\CachedByProxy;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Throwable;

/**
 * Class User Repository
 */
final class UserRepository extends Repository implements UserRepositoryInterface
{
    /**
     * Set Repository Model
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = app(User::class);
    }

    /**
     * Get All Users
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
                ->from(table: $this->model::getTable(), as: 'user')
                ->select(['user.id', 'user.email'])
                ->orderBy('user.id', 'ASC')
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
     * TODO: Needs to be implemented
     *
     * @param array|Collection $resource
     * @return int|JsonModel|bool
     * @throws ValidationException
     */
    public function create(array|Collection $resource): int|JsonModel|bool
    {
        throw new ValidationException(errorCode: Codes::GENERIC_ERROR);
    }

    /**
     * TODO: Needs to be implemented
     *
     * @param int $id
     * @param Collection $resource
     * @return int|JsonModel|bool
     * @throws ValidationException
     */
    public function update(int $id, Collection $resource): int|JsonModel|bool
    {
        throw new ValidationException(errorCode: Codes::GENERIC_ERROR);
    }

    /**
     * TODO: Needs to be implemented
     * @param int $id
     * @throws ValidationException
     */
    public function delete(int $id): bool
    {
        throw new ValidationException(errorCode: Codes::GENERIC_ERROR);
    }
}
