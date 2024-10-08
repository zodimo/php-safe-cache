<?php

declare(strict_types=1);

namespace SafeCache\Models;

/**
 * @template DEFAULT
 */
class CacheGetMultipleRequest
{
    /**
     * @param iterable<string> $keys
     * @param mixed            $default
     */
    private function __construct(
        private iterable $keys,
        private $default,
    ) {}

    /**
     * @template _DEFAULT
     *
     * @param iterable<string> $keys    a list of keys that can be obtained in a single operation
     * @param ?_DEFAULT        $default default value to return if the key does not exist
     *
     * @return CacheGetMultipleRequest<_DEFAULT>
     */
    public static function create(iterable $keys, $default = null): CacheGetMultipleRequest
    {
        return new self(
            $keys,
            $default,
        );
    }

    /**
     * @return iterable<string>
     */
    public function getKeys(): iterable
    {
        return $this->keys;
    }

    /**
     * @return DEFAULT
     */
    public function getDefault()
    {
        return $this->default;
    }
}
