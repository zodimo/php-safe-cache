<?php

declare(strict_types=1);

namespace SafeCache\Models;

use DateInterval;
use Psr\Clock\ClockInterface;
use Psr\SimpleCache\CacheInterface;
use Traversable;

class ArrayCache implements CacheInterface
{
    /**
     * @var array<string,CachedItem<mixed>>
     */
    private array $cache;

    public function __construct(
        private ClockInterface $clock,
    ) {
        $this->cache = [];
    }

    public function clear(): bool
    {
        $this->cache = [];

        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->cache[$key]);

        return true;
    }

    public function deleteMultiple(array|Traversable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $item = $this->cache[$key];
        if (!is_null($item)) {
            return $item->getValue();
        }

        return $default;
    }

    public function getMultiple(array|Traversable $keys, mixed $default = null): array|Traversable
    {
        $output = [];
        foreach ($keys as $key) {
            $output = $this->get($key, $default);
        }

        return $output;
    }

    public function has(string $key): bool
    {
        if (key_exists($key, $this->cache)) {
            $cachedItem = $this->cache[$key];
            if ($cachedItem->isActive()) {
                return true;
            }
            unset($this->cache[$key]);
        }

        return false;
    }

    public function set(string $key, mixed $value, null|DateInterval|int $ttl = null): bool
    {
        $expireTime = null;
        if (!is_null($ttl)) {
            if (!$ttl instanceof DateInterval) {
                $ttlInterval = new DateInterval("PT{$ttl}S");
            } else {
                $ttlInterval = $ttl;
            }
            $expireTime = $this->clock->now()->add($ttlInterval);
        }

        $cachedItem = CachedItem::create($value, $this->clock, $expireTime);
        $this->cache[$key] = $cachedItem;

        return true;
    }

    /**
     * @param array<string,mixed>|Traversable<string,mixed> $values
     */
    public function setMultiple(array|Traversable $values, null|DateInterval|int $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }
}
