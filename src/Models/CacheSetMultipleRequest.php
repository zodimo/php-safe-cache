<?php

declare(strict_types=1);

namespace SafeCache\Models;

use DateInterval;
use Zodimo\BaseReturn\Option;

class CacheSetMultipleRequest
{
    /**
     * @param iterable<string,mixed> $values
     * @param Option<DateInterval>   $ttlIntervalOption
     */
    private function __construct(
        private iterable $values,
        private Option $ttlIntervalOption
    ) {}

    /**
     * @param iterable<string,mixed> $values a list of key => value pairs for a multiple-set operation
     * @param null|DateInterval|int  $ttl    Optional. The TTL value of this item. If no value is sent and
     *                                       the driver supports TTL then the library may set a default value
     *                                       for it or let the driver take care of that.
     */
    public static function create(iterable $values, null|DateInterval|int $ttl = null): CacheSetMultipleRequest
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
            $values,
            $ttlIntervalOption
        );
    }

    /**
     * @return iterable<string,mixed>
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return Option<DateInterval>
     */
    public function getTtl(): Option
    {
        return $this->ttlIntervalOption;
    }
}
