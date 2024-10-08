<?php

declare(strict_types=1);

namespace SafeCache\Caches;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use SafeCache\Exceptions\CacheException;
use SafeCache\Models\CacheGetMultipleRequest;
use SafeCache\Models\CacheGetRequest;
use SafeCache\Models\CacheSetMultipleRequest;
use SafeCache\Models\CacheSetRequest;
use SafeCache\SafeCacheInterface;
use Throwable;
use Traversable;
use Zodimo\BaseReturn\IOMonad;

class SafeCacheWrap implements SafeCacheInterface
{
    public function __construct(private CacheInterface $unsafeCache) {}

    public function clear(): IOMonad
    {
        try {
            $result = $this->unsafeCache->clear();
            if (false == $result) {
                return IOMonad::fail(CacheException::createForUnspecifiedError());
            }

            return IOMonad::pure(null);
        } catch (Throwable $e) {
            return IOMonad::fail(CacheException::createForUnspecifiedException($e));
        }
    }

    public function delete(string $key): IOMonad
    {
        try {
            $result = $this->unsafeCache->delete($key);
            if (false == $result) {
                return IOMonad::fail(CacheException::createForUnspecifiedError());
            }

            return IOMonad::pure(null);
        } catch (InvalidArgumentException $e) {
            return IOMonad::fail(CacheException::createForInvalidArgument($e));
        } catch (Throwable $e) {
            return IOMonad::fail(CacheException::createForUnspecifiedException($e));
        }
    }

    public function deleteMultiple(array|Traversable $keys): IOMonad
    {
        try {
            $result = $this->unsafeCache->deleteMultiple($keys);
            if (false == $result) {
                return IOMonad::fail(CacheException::createForUnspecifiedError());
            }

            return IOMonad::pure(null);
        } catch (InvalidArgumentException $e) {
            return IOMonad::fail(CacheException::createForInvalidArgument($e));
        } catch (Throwable $e) {
            return IOMonad::fail(CacheException::createForUnspecifiedException($e));
        }
    }

    public function get(CacheGetRequest $request): IOMonad
    {
        try {
            $result = $this->unsafeCache->get($request->getKey(), $request->getDefault());

            return IOMonad::pure($result);
        } catch (InvalidArgumentException $e) {
            return IOMonad::fail(CacheException::createForInvalidArgument($e));
        } catch (Throwable $e) {
            return IOMonad::fail(CacheException::createForUnspecifiedException($e));
        }
    }

    public function getMultiple(CacheGetMultipleRequest $request): IOMonad
    {
        try {
            $result = $this->unsafeCache->getMultiple($request->getKeys(), $request->getDefault());

            return IOMonad::pure($result);
        } catch (InvalidArgumentException $e) {
            return IOMonad::fail(CacheException::createForInvalidArgument($e));
        } catch (Throwable $e) {
            return IOMonad::fail(CacheException::createForUnspecifiedException($e));
        }
    }

    public function has(string $key): IOMonad
    {
        try {
            $result = $this->unsafeCache->has($key);

            return IOMonad::pure($result);
        } catch (InvalidArgumentException $e) {
            return IOMonad::fail(CacheException::createForInvalidArgument($e));
        } catch (Throwable $e) {
            return IOMonad::fail(CacheException::createForUnspecifiedException($e));
        }
    }

    public function set(CacheSetRequest $request): IOMonad
    {
        try {
            $key = $request->getKey();
            $value = $request->getValue();
            $result = $request->getTtl()->match(
                fn ($ttl) => $this->unsafeCache->set($key, $value, $ttl),
                fn () => $this->unsafeCache->set($key, $value),
            );
            if (false == $result) {
                return IOMonad::fail(CacheException::createForUnspecifiedError());
            }

            return IOMonad::pure(null);
        } catch (InvalidArgumentException $e) {
            return IOMonad::fail(CacheException::createForInvalidArgument($e));
        } catch (Throwable $e) {
            return IOMonad::fail(CacheException::createForUnspecifiedException($e));
        }
    }

    public function setMultiple(CacheSetMultipleRequest $request): IOMonad
    {
        try {
            $values = $request->getValues();
            $result = $request->getTtl()->match(
                fn ($ttl) => $this->unsafeCache->setMultiple($values, $ttl),
                fn () => $this->unsafeCache->setMultiple($values),
            );
            if (false == $result) {
                return IOMonad::fail(CacheException::createForUnspecifiedError());
            }

            return IOMonad::pure(null);
        } catch (InvalidArgumentException $e) {
            return IOMonad::fail(CacheException::createForInvalidArgument($e));
        } catch (Throwable $e) {
            return IOMonad::fail(CacheException::createForUnspecifiedException($e));
        }
    }
}
