<?php

declare(strict_types=1);

namespace SafeCache\Tests;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

class ControllableClock implements ClockInterface
{
    private DateTimeImmutable $now;

    public function __construct(?DateTimeImmutable $now = null)
    {
        $this->now = $now ?? new DateTimeImmutable();
    }

    public function setNow(DateTimeImmutable $now): void
    {
        $this->now = $now;
    }

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }
}
