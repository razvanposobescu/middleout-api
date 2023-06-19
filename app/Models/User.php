<?php

namespace App\Models;

/**
 * User Model
 */
final class User extends JsonModel
{
    /**
     * @var int $id
     */
    public int $id;

    /**
     * @var string $email
     */
    public string $email;

    /**
     * Database Table
     *
     * @var string|null
     */
    protected static ?string $table = 'users';

    /**
     * Array with DB Columns
     *
     * @var array|string[]
     */
    protected static ?array $columns = [
        'id',
        'email',
    ];

    /**
     * We only want to expose the email :)
     *
     * @return string[]
     */
    public function toJson(): array
    {

        return [
            // 'id' => $this->id,
            'email' => $this->email,
        ];
    }

    /**
     * Return Record as Array
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
        ];
    }
}
