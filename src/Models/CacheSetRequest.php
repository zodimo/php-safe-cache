<?php

declare(strict_types=1);

namespace SafeCache\Models;

use DateInterval;
use Zodimo\BaseReturn\Option;

class CacheSetRequest
{
    /**
     * @param Option<DateInterval> $ttlIntervalOption
     * @param mixed                $value
     */
    private function __construct(
        private string $key,
        private $value,
        private Option $ttlIntervalOption
    ) {}

    /**
     * @param string                $key   the key of the item to store
     * @param mixed                 $value the value of the item to store, must be serializable
     * @param null|DateInterval|int $ttl   Optional. The TTL value of this item. If no value is sent and
     *                                     the driver supports TTL then the library may set a default value
     *                                     for it or let the driver take care of that.
     */
    public static function create(string $key, $value, null|DateInterval|int $ttl = null): CacheSetRequest
    {
        $ttlIntervalOption = Option::none();

        if (!is_null($ttl)) {
            if ($ttl instanceof DateInterval) {
                $ttlIntervalOption = Option::some($ttl);
            } else {
                $intervalString = "PT{$ttl}S";
                $ttlIntervalOption = Option::some(new DateInterval($intervalString));
            }
        }

        return new self(
            $key,
            $value,
            $ttlIntervalOption
        );
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return Option<DateInterval>
     */
    public function getTtl(): Option
    {
        return $this->ttlIntervalOption;
    }
}
