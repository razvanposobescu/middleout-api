<?php

namespace App\Repositories\Article;

use App\Models\Article;
use App\Repositories\Repository;

/**
 *
 */
final class ArticleRepository extends Repository implements ArticleRepositoryInterface
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
}
