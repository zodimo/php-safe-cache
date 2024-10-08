<?php

declare(strict_types=1);

namespace SafeCache\Models;

/**
 * @template DEFAULT
 */
class CacheGetRequest
{
    /**
     * @param mixed $default
     */
    private function __construct(
        private string $key,
        private $default,
    ) {}

    /**
     * @template _DEFAULT
     *
     * @param string    $key     the unique key of this item in the cache
     * @param ?_DEFAULT $default default value to return if the key does not exist
     *
     * @return CacheGetRequest<_DEFAULT>
     */
    public static function create(string $key, $default = null): CacheGetRequest
    {
        return new self(
            $key,
            $default,
        );
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return DEFAULT
     */
    public function getDefault()
    {
        return $this->default;
    }
}
