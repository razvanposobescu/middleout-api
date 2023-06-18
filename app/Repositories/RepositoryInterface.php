<?php

namespace App\Repositories;

use App\Models\JsonModel;
use Illuminate\Support\Collection;

/**
 * Repository Interface
 */
interface RepositoryInterface
{
    /**
     * @return Collection|null
     */
    public function all(): ?Collection;

    /**
     * Get Resource by id
     *
     * @param int $id
     * @return JsonModel|null
     */
    public function getById(int $id): ?JsonModel;

    /**
     * Update Resource
     *
     * @param int $id
     * @param Collection $resource
     * @return JsonModel|bool|null
     */
    public function update(int $id, Collection $resource): JsonModel|bool|null;

    /**
     * Create Resource
     *
     * @param array|Collection $resource
     * @return JsonModel|bool|null
     */
    public function create(array|Collection $resource): JsonModel|bool|null;

    /**
     * Delete Resource by id
     *
     * @param int $id
     * @return mixed
     */
    public function delete(int $id): bool;
}
