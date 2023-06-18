<?php

namespace App\Services\Cache;


use App\Repositories\RepositoryInterface;

use App\Services\Cache\Attributes\CachedByProxy;
use App\Services\Service;
use App\Traits\Singleton;

use Illuminate\Filesystem\FilesystemAdapter;

use ProxyManager\Configuration;
use ProxyManager\Factory\AccessInterceptorValueHolderFactory;
use ProxyManager\FileLocator\FileLocator;
use ProxyManager\GeneratorStrategy\FileWriterGeneratorStrategy;
use ProxyManager\Proxy\AccessInterceptorInterface;
use ProxyManager\Proxy\ValueHolderInterface;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use ReflectionClass;
use ReflectionMethod;


/**
 * Cache Interceptor Service
 */
final class RepositoryCacheInterceptorService extends Service
{
    use Singleton;

    /**
     * @param RepositoryInterface $object
     * @param int|bool $cacheTtl
     * @param array $cacheTags
     * @return ValueHolderInterface|AccessInterceptorInterface
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
	public function addProxyToRepository(RepositoryInterface $object, array $cacheTags = [], int|bool $cacheTtl = false): ValueHolderInterface|AccessInterceptorInterface
    {
        return (new AccessInterceptorValueHolderFactory($this->proxyMangerConfig()))
            ->createProxy(
                $object,
                $this->preFunctions($object, $cacheTags),
                $this->postFunctions($object, $cacheTags, $cacheTtl)
            );
    }

    /**
     * @param object $object
     * @param array $cacheTags
     * @return array
     */
	private function preFunctions(object $object, array $cacheTags = []): array
    {
		$publicMethods = $this->getPublicMethods($object);
		$preFunctions = [];

		foreach ($publicMethods as $reflectionMethod)
        {
            /**
             * @param $proxy
             * @param $instance
             * @param $method
             * @param $params
             * @param $returnEarly
             * @return mixed|void
             */
			$preFunctions[$reflectionMethod->name] = function ($proxy, $instance, $method, $params, &$returnEarly) use ($cacheTags)
            {
                // here we can add extra complex logic or just get the cache
                $key = $this->generateKey(get_class($instance), $method, json_encode($params));

                $cachedResult = CacheService::make()->tags($cacheTags)->get($key);

                if ($cachedResult)
                {
                    $returnEarly = true;
                    return $cachedResult;
                }
			};

            return $preFunctions;
		}

		return $preFunctions;
	}

	/**
	 * @param object $object
	 * @param int|bool $cacheTtl false = disabled, true = cache forever, {number} = ttl in seconds.
	 * @param array $cacheTags Any extra cache tags to be attached to the cached object(s).
	 * @return array
	 */
	private function postFunctions(object $object, array $cacheTags = [], int|bool $cacheTtl = false): array
    {
        $postFunctions = [];
        $publicMethods = $this->getPublicMethods($object);
        foreach ($publicMethods as $reflectionMethod)
        {
            $postFunctions[$reflectionMethod->name] = function ($proxy, $instance, $method, $params, $returnValue, &$returnEarly) use ($cacheTags, $cacheTtl)
            {
                if ($instance === $returnValue)
                {
                    $returnEarly = true;
                    return $proxy;
                }

                // We don't want to use this in the key as it's a flag to prevent an infinite loop
                $key = $this->generateKey(get_class($instance), $method, json_encode($params));

                // put result in cache
                CacheService::make()->tags($cacheTags)->setTtl($cacheTtl)->put($key, $returnValue);

                return false;
            };
        }

        return $postFunctions;
	}

    /**
     * Proxy only Public Methods that have the CachedByProxy Attribute
     * @param object $object
     * @return array
     */
	private function getPublicMethods(object $object): array
    {
		$publicMethods = (new ReflectionClass($object))->getMethods(ReflectionMethod::IS_PUBLIC);
		$publicCacheableMethods = [];

        foreach ($publicMethods as $method)
        {
            // only proxy methods that have the CacheByProxy Attribute otherwise
            // the reason for the attribute is: we don't want cache on RUD methods in the repos
            if (count($method->getAttributes(CachedByProxy::class)) > 0)
            {
                // Remove magic methods like __construct
                if (!str_contains($method->name, '__'))
                {
                    $publicCacheableMethods[] = $method;
                }
            }
		}

		return $publicCacheableMethods;
	}

    /**
     * Generate Cache Key
     *
     * @param ...$vars
     * @return string
     */
	private function generateKey(...$vars): string
    {
		return md5(implode(':', $vars));
	}

    /**
     * Configure The Proxy Manager
     *
     * @return Configuration
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function proxyMangerConfig(): Configuration
    {
        // get a proxy manager config
        $config = new Configuration();


        // get boostrap folder path
        $proxiedRepositoriesFolder =  app()->bootstrapPath('cache/proxies');

        /**
         * @var FilesystemAdapter $fileSystem
         */
        $filesystem = app()->get('filesystem')->disk();
        if (!$filesystem->exists($proxiedRepositoriesFolder))
        {
            $filesystem->makeDirectory($proxiedRepositoriesFolder);
        }

        // generate the proxies and store them as files
        $config->setGeneratorStrategy(
            new FileWriterGeneratorStrategy(
                new FileLocator($proxiedRepositoriesFolder)
            )
        );

        // set the directory to read the generated proxies from
        $config->setProxiesTargetDir($proxiedRepositoriesFolder);

        // then register the autoloader
        spl_autoload_register($config->getProxyAutoloader());

        return $config;
    }
}
