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
 * TODO: Do not use from Laravel any Facades or helper functions that use dependencies from the container/service manager.
 * TODO: Kinda hard not to use the App Facade or the DI container as it's in all the inner works of laravel
 * TODO: since we want to use the proxy manger package to cache results need to find a better way to implement it.
 *
 * Let's use the proxy pattern to cache the results :)
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
     public function register()
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
                cacheTags: ['articles'], // cache for 60 seconds
                cacheTtl: 60
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

    /**
     *
     */
    public function boot(): void
    {

    }
}
