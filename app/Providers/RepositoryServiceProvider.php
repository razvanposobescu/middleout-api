<?php

namespace App\Providers;

use App\Repositories\Article\ArticleRepository;
use App\Services\Cache\RepositoryCacheInterceptorService;
use Illuminate\Support\ServiceProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Repository Service Provider
 *
 * Let's use the proxy pattern to cache the results :)
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
     public function register(): void
     {
        try
        {
            /**
             * Register Repositories
             */
            $proxyCacheService = RepositoryCacheInterceptorService::make();

            // we want laravel to handle the DI
            $ArticleRepositoryInstance = $proxyCacheService->addProxyToRepository(
                object: $this->app->get(ArticleRepository::class),
                cacheTags: ['articles'], // cache tag
                cacheTtl: config('cache.ttl') // cache ttl set up in env file or default from cfg
            );

            // register the Proxy the ArticleRepository to leverage the cache
            $this->app->singleton(ArticleRepository::class, fn() => $ArticleRepositoryInstance);

        }
        catch (NotFoundExceptionInterface|ContainerExceptionInterface $exception)
        {
            // do some error logic here if needed for the sakes of the test we just dd it
            dd($exception);
        }
    }
}
