<?php

namespace App\Services\Cache;

use App\Models\User;
use App\Services\Service;
use App\Traits\Singleton;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Just a Basic Cache Service that can also handle extra logic if needed.
 */
final class CacheService extends Service
{
    /**
     * Use the Singleton design pattern
     */
    use Singleton;

    /**
     * Tags
     *
     * @var array $tags
     */
    private array $tags = [];

    /**
     * Cache TTL
     *
     * @var int $ttl
     */
    private int $ttl;

    /**
     * Cache Repository
     *
     * @var CacheContract
     */
    private CacheContract $cacheRepository;

    protected function __construct()
    {
        // set config TTL
        $this->ttl = config('cache.ttl');

        // get the cache repo
        $this->cacheRepository = app(\Illuminate\Cache\Repository::class);
    }

    /**
     * Cache TTL
     * @param int $ttl
     * @return self
     */
    public function setTtl(int $ttl = 0): self
    {
        $this->ttl = $ttl;
        return $this;
    }

    /**
     * Tag you're it :)
     *
     * @param array $tags
     * @return self;
     */
    public function tags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * We can add extra functionality here :)
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function get(string $key): mixed
    {
        return $this->cacheRepository->tags($this->tags)->get($key);
    }

    public function put(string $key, $value): bool
    {
        return $this->cacheRepository->tags($this->tags)->put($key, $value, $this->ttl);
    }

    /**
     * Forget given gey from cache
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        return $this->cacheRepository->forget($key);
    }

    /**
     * Flush the Cache
     *
     * @param array $tags
     * @return void
     */
    public function flush(array $tags = []): void
    {
       $this->cacheRepository->flush();
    }
}
