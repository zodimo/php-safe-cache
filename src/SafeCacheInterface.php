<?php

declare(strict_types=1);

namespace SafeCache;

use SafeCache\Exceptions\CacheException;
use SafeCache\Models\CacheGetMultipleRequest;
use SafeCache\Models\CacheGetRequest;
use SafeCache\Models\CacheSetMultipleRequest;
use SafeCache\Models\CacheSetRequest;
use Zodimo\BaseReturn\IOMonad;

interface SafeCacheInterface
{
    /**
     * Fetches a value from the cache.
     *
     * @param CacheGetRequest<?mixed> $request
     *
     * @return IOMonad<mixed,CacheException> the value of the item from the cache, or $default in case of cache miss
     */
    public function get(CacheGetRequest $request): IOMonad;

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @return IOMonad<null,CacheException> true on success and false on failure
     */
    public function set(CacheSetRequest $request): IOMonad;

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key the unique cache key of the item to delete
     *
     * InvalidArgumentException  MUST be thrown if the $key string is not a legal value
     *   MUST be thrown if the $key string is not a legal value
     *
     * @return IOMonad<null,CacheException> True if the item was successfully removed. False if there was an error.
     */
    public function delete(string $key): IOMonad;

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return IOMonad<null,CacheException> true on success and false on failure
     */
    public function clear(): IOMonad;

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param CacheGetMultipleRequest<?mixed> $request
     *
     * @return IOMonad<iterable<string,mixed>,CacheException> A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     */
    public function getMultiple(CacheGetMultipleRequest $request): IOMonad;

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @return IOMonad<null,CacheException> true on success and false on failure
     */
    public function setMultiple(CacheSetMultipleRequest $request): IOMonad;

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable<string> $keys a list of string-based keys to be deleted
     *
     * InvalidArgumentException
     *                                  MUST be thrown if $keys is neither an array nor a Traversable,
     *                                  or if any of the $keys are not a legal value
     *
     * @return IOMonad<null,CacheException> True if the items were successfully removed. False if there was an error.
     */
    public function deleteMultiple(iterable $keys): IOMonad;

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $key the cache item key
     *
     * InvalidArgumentException
     *                    MUST be thrown if the $key string is not a legal value
     *
     * @return IOMonad<bool,CacheException>
     */
    public function has(string $key): IOMonad;
}
