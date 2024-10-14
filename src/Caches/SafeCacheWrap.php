<?php

declare(strict_types=1);

namespace SafeCache\Caches;

use KindErrors\ErrorContext;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use SafeCache\Models\CacheGetMultipleRequest;
use SafeCache\Models\CacheGetRequest;
use SafeCache\Models\CacheSetMultipleRequest;
use SafeCache\Models\CacheSetRequest;
use SafeCache\SafeCacheErrorService;
use SafeCache\SafeCacheInterface;
use Throwable;
use Traversable;
use Zodimo\BaseReturn\IOMonad;

class SafeCacheWrap implements SafeCacheInterface
{
    private SafeCacheErrorService $errorService;

    public function __construct(private CacheInterface $unsafeCache)
    {
        $this->errorService = new SafeCacheErrorService();
    }

    public function clear(): IOMonad
    {
        $lazyContext = fn () => ErrorContext::create()->set('action', 'clear');

        try {
            $result = $this->unsafeCache->clear();
            if (false == $result) {
                return IOMonad::fail($this->errorService->createForFalseReturnValue($lazyContext()));
            }

            return IOMonad::pure(null);
        } catch (Throwable $e) {
            return IOMonad::fail($this->errorService->createForCacheException($e, $lazyContext()));
        }
    }

    public function delete(string $key): IOMonad
    {
        $lazyContext = fn () => ErrorContext::create()->set('action', 'delete')->set('key', $key);

        try {
            $result = $this->unsafeCache->delete($key);
            if (false == $result) {
                return IOMonad::fail($this->errorService->createForFalseReturnValue($lazyContext()));
            }

            return IOMonad::pure(null);
        } catch (InvalidArgumentException $e) {
            return IOMonad::fail($this->errorService->createForInvalidArgumentException($e, $lazyContext()));
        } catch (Throwable $e) {
            return IOMonad::fail($this->errorService->createForCacheException($e, $lazyContext()));
        }
    }

    public function deleteMultiple(array|Traversable $keys): IOMonad
    {
        $lazyContext = fn () => ErrorContext::create()->set('action', 'deleteMultiple')->set('keys', $keys);

        try {
            $result = $this->unsafeCache->deleteMultiple($keys);
            if (false == $result) {
                return IOMonad::fail($this->errorService->createForFalseReturnValue($lazyContext()));
            }

            return IOMonad::pure(null);
        } catch (InvalidArgumentException $e) {
            return IOMonad::fail($this->errorService->createForInvalidArgumentException($e, $lazyContext()));
        } catch (Throwable $e) {
            return IOMonad::fail($this->errorService->createForCacheException($e, $lazyContext()));
        }
    }

    public function get(CacheGetRequest $request): IOMonad
    {
        $lazyContext = fn () => ErrorContext::create()->set('action', 'get')->set('request', $request);

        try {
            $result = $this->unsafeCache->get($request->getKey(), $request->getDefault());

            return IOMonad::pure($result);
        } catch (InvalidArgumentException $e) {
            return IOMonad::fail($this->errorService->createForInvalidArgumentException($e, $lazyContext()));
        } catch (Throwable $e) {
            return IOMonad::fail($this->errorService->createForCacheException($e, $lazyContext()));
        }
    }

    public function getMultiple(CacheGetMultipleRequest $request): IOMonad
    {
        $lazyContext = fn () => ErrorContext::create()->set('action', 'getMultiple')->set('request', $request);

        try {
            $result = $this->unsafeCache->getMultiple($request->getKeys(), $request->getDefault());

            return IOMonad::pure($result);
        } catch (InvalidArgumentException $e) {
            return IOMonad::fail($this->errorService->createForInvalidArgumentException($e, $lazyContext()));
        } catch (Throwable $e) {
            return IOMonad::fail($this->errorService->createForCacheException($e, $lazyContext()));
        }
    }

    public function has(string $key): IOMonad
    {
        $lazyContext = fn () => ErrorContext::create()->set('action', 'has')->set('key', $key);

        try {
            $result = $this->unsafeCache->has($key);

            return IOMonad::pure($result);
        } catch (InvalidArgumentException $e) {
            return IOMonad::fail($this->errorService->createForInvalidArgumentException($e, $lazyContext()));
        } catch (Throwable $e) {
            return IOMonad::fail($this->errorService->createForCacheException($e, $lazyContext()));
        }
    }

    public function set(CacheSetRequest $request): IOMonad
    {
        $lazyContext = fn () => ErrorContext::create()->set('action', 'set')->set('request', $request);

        try {
            $key = $request->getKey();
            $value = $request->getValue();
            $result = $request->getTtl()->match(
                fn ($ttl) => $this->unsafeCache->set($key, $value, $ttl),
                fn () => $this->unsafeCache->set($key, $value),
            );
            if (false == $result) {
                return IOMonad::fail($this->errorService->createForFalseReturnValue($lazyContext()));
            }

            return IOMonad::pure(null);
        } catch (InvalidArgumentException $e) {
            return IOMonad::fail($this->errorService->createForInvalidArgumentException($e, $lazyContext()));
        } catch (Throwable $e) {
            return IOMonad::fail($this->errorService->createForCacheException($e, $lazyContext()));
        }
    }

    public function setMultiple(CacheSetMultipleRequest $request): IOMonad
    {
        $lazyContext = fn () => ErrorContext::create()->set('action', 'setMultiple')->set('request', $request);

        try {
            $values = $request->getValues();
            $result = $request->getTtl()->match(
                fn ($ttl) => $this->unsafeCache->setMultiple($values, $ttl),
                fn () => $this->unsafeCache->setMultiple($values),
            );
            if (false == $result) {
                return IOMonad::fail($this->errorService->createForFalseReturnValue($lazyContext()));
            }

            return IOMonad::pure(null);
        } catch (InvalidArgumentException $e) {
            return IOMonad::fail($this->errorService->createForInvalidArgumentException($e, $lazyContext()));
        } catch (Throwable $e) {
            return IOMonad::fail($this->errorService->createForCacheException($e, $lazyContext()));
        }
    }
}
