<?php

declare(strict_types=1);

namespace SafeCache\Models;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

/**
 * @template T
 */
class CachedItem
{
    /**
     * @param T $value
     */
    private function __construct(
        private $value,
        private ClockInterface $clock,
        private ?DateTimeImmutable $expireTime = null,
    ) {}

    /**
     * @template NEW_T
     *
     * @param NEW_T $value
     *
     * @return CachedItem<NEW_T>
     */
    public static function create($value, ClockInterface $clock, ?DateTimeImmutable $expireTime = null): CachedItem
    {
        return new self($value, $clock, $expireTime);
    }

    public function isActive(): bool
    {
        if ($this->expireTime instanceof DateTimeImmutable) {
            return $this->clock->now() <= $this->expireTime;
        }

        return true;
    }

    /**
     * @return T
     */
    public function getValue()
    {
        return $this->value;
    }
}
