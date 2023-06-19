<?php

namespace App\Traits;

use App\Enums\Errors\Codes;
use App\Exceptions\InvalidDataTypeException;
use App\Models\JsonModel;
use Illuminate\Support\Collection;

use JetBrains\PhpStorm\NoReturn;
use \JsonMapper as JsonMapperInstance;

trait JsonMapper
{
    /**
     * @param object $data
     * @return JsonModel|Collection
     * @throws InvalidDataTypeException
     */
    #[NoReturn] protected function mapData(mixed $data): JsonModel|Collection
    {
        $data = json_decode(json_encode($data));

        if (is_object($data))
        {
            return $this->mapObject($data);
        }
        elseif (is_array($data))
        {
            return $this->mapArray($data);
        }

        throw new InvalidDataTypeException(Codes::INVALID_ARGUMENT);
    }

    /**
     *  Map a single object
     *
     * @param object $object
     * @return JsonModel
     */
    protected function mapObject(object $object): JsonModel
    {
        return $this->getMapper()->map($object, new $this->model());
    }

    /**
     * Map an array of objects
     *
     * @param array $data
     * @return mixed
     */
    protected function mapArray(array $data): Collection
    {
        return $this->getMapper()->mapArray($data, new Collection(), $this->model::class);
    }

    /**
     * Get Json Mapper Instance
     *
     * @return mixed
     */
    private function getMapper(): mixed
    {
        return app(JsonMapperInstance::class);
    }
}
