<?php

namespace App\Repositories;

use App\Traits\JsonMapper;

use App\Models\JsonModel;

use App\Enums\Errors\Codes;

use App\Exceptions\{
    InvalidDataTypeException,
    InvalidModelException,
    ValidationException
};

use Illuminate\Support\Collection;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilderContract;
use Illuminate\Database\Query\{
    Builder,
    Grammars\MySqlGrammar,
    Processors\MySqlProcessor
};

use Throwable;

/**
 * Repository Interface
 */
abstract class Repository
{
    use JsonMapper;

    /**
     * Database Transaction Attempts
     */
    private const DB_TRANSACTION_ATTEMPTS = 3;

    /**
     * Query Builder used by Repos
     *
     * @var QueryBuilderContract $queryBuilder
     */
    protected QueryBuilderContract $queryBuilder;

    /**
     * Repository Model
     *
     * @var JsonModel
     */
    protected JsonModel $model;

    /**
     * @throws InvalidModelException
     */
    public function __construct(Builder $databaseManager)
    {
        // set model && validate it
        $this->setModel();
        $this->validateModel();

        // set query builder
        $this->setQueryBuilder($databaseManager);
    }

    /**
     * Get a new Instance of the Query Builder
     *
     * @return Builder
     */
    public function newQuery(): QueryBuilderContract
    {
        return $this->queryBuilder->newQuery()
            ->from($this->model::getTable())
            ->select($this->model::getColumns());
    }

    /**
     * Set Repository Model
     *
     * @return void
     */
    protected abstract function setModel(): void;

    /**
     * Update
     *
     * @param int $id
     * @param Collection $resource
     * @return int|JsonModel|bool
     */
    public abstract function update(int $id, Collection $resource): int|JsonModel|bool;

    /**
     * create
     *
     * @param array|Collection $resource
     * @return int|JsonModel|bool
     */
    public abstract function create(array|Collection $resource): int|JsonModel|bool;

    /**
     * Delete
     *
     * @param int $id
     * @return bool
     */
    public abstract function delete(int $id): bool;

    /**
     * fetch all data from db
     *
     * @return Collection|null
     * @throws InvalidDataTypeException|ValidationException
     */
    public abstract function all(): ?Collection;

    /**
     * Get By id
     *
     * @param int $id
     * @return Collection|null
     * @throws Throwable
     */
    public function getById(int $id): ?JsonModel
    {
        try
        {
            // run basic select by id
            $result = $this->newQuery()->where(['id' => $id])->get()->firstOrFail();

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
     * Set Query Builder used by Repositories
     *
     * TODO: here we can even specify what type of sql builder if we wanted
     * TODO: Set a new DB Connection being mysql, postgres etc.
     * TODO: set new Grammar and Processor and we should be good to go
     *
     * @param QueryBuilderContract $builder
     * @return void
     */
    protected function setQueryBuilder(QueryBuilderContract $builder): void
    {
        // since we need to use the query builder without the DB facade
        // we need to re-initiate the sql grammar language model and the processor

        // new instance of sql grammar is needed since the DI container is not used.
        $builder->grammar = new MySqlGrammar;

        // set mysql processor if we don't have one
        $builder->processor ??= new MySqlProcessor;

        // ref instance in memory
        $this->queryBuilder = $builder;
    }

    /**
     * Basic Validation Data
     *
     * @throws InvalidModelException
     */
    private function validateModel(): void
    {
        if (!$this->model::getTable() || empty($this->model::getColumns()))
        {
            throw new InvalidModelException(Codes::INVALID_INSTANCE);
        }
    }
}
