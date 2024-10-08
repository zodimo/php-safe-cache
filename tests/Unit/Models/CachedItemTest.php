<?php

declare(strict_types=1);

namespace SafeCache\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use SafeCache\Models\CachedItem;
use SafeCache\Tests\ControllableClock;

/**
 * @internal
 *
 * @coversNothing
 */
class CachedItemTest extends TestCase
{
    public function createControllableClock(?\DateTimeImmutable $initialNow = null): ControllableClock
    {
        $initialNow ??= new \DateTimeImmutable();

        return new ControllableClock($initialNow);
    }

    public function testCanCreate(): void
    {
        $clock = $this->createMock(ClockInterface::class);
        $item = CachedItem::create(10, $clock);
        $this->assertInstanceOf(CachedItem::class, $item);
    }

    public function testItemCanExpire(): void
    {
        $now = new \DateTimeImmutable();
        $clock = $this->createControllableClock($now);
        $ttl = 10;
        $pastTtl = $ttl + 1;
        $expireTime = $clock->now()->add(new \DateInterval("PT{$ttl}S"));
        $clockPastedTtl = $clock->now()->add(new \DateInterval("PT{$pastTtl}S"));
        $item = CachedItem::create(10, $clock, $expireTime);
        $this->assertTrue($item->isActive(), 'still active');
        $clock->setNow($clockPastedTtl);
        $this->assertFalse($item->isActive(), 'not active');
    }
}
