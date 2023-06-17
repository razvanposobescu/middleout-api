<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Just to see the Database
 */
class Article extends JsonModel
{
    /**
     * Database Table
     *
     * @var string|null
     */
    protected static ?string $table = 'articles';

    /**
     * db columns
     *
     * @var array<int, string>
     */
    protected static ?array $columns = [
        'user_id',
        'title',
        'body',
        'published_at',
    ];

}
