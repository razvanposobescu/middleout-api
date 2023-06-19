<?php

namespace App\Repositories\Article;


use App\Repositories\RepositoryInterface;

use Illuminate\Support\Collection;

interface ArticleRepositoryInterface extends RepositoryInterface
{
    // add extra methods here

    /**
     * Create or update the values
     *
     * @param array|Collection $resource
     * @return mixed
     */
    public function createOrUpdate(array|Collection $resource): mixed;
}
