<?php

namespace App\Models;

/**
 * Just to see the Database
 */
class Article extends JsonModel
{
    /**
     * Article ID
     *
     * @var int $id
     */
    public int $id;

    /**
     * User Id
     *
     * @var int $user_id
     */
    public int $user_id;

    /**
     * @var User $user
     */
    public User $user;

    /**
     * @var string $title
     */
    public string $title;

    /**
     * @var string $body
     */
    public string $body;

    /**
     * @var \DateTime|null $published_at
     */
    public ?\DateTime $published_at;

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
        'id',
        'user_id',
        'title',
        'body',
        'published_at',
    ];

    /**
     * Json Fields
     *
     * @return array
     */
    public function toJson(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'isPublished' => $this->isPublished(),
            'published_at' => $this->published_at,
            'user' => $this->user,
        ];
    }

    /**
     * Determinate if an article is published or not
     *
     * @return bool
     */
    public function isPublished(): bool
    {
        return ($this->published_at instanceof \DateTime);
    }
}
