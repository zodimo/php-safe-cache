<?php

declare(strict_types=1);

namespace SafeCache\Tests\Unit;

use KindErrors\ErrorContext;
use KindErrors\KindError;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\InvalidArgumentException;
use SafeCache\Errors\SafeCacheErrorKind;
use SafeCache\SafeCacheErrorService;
use Throwable;

/**
 * @internal
 *
 * @coversNothing
 */
class SafeCacheErrorServiceTest extends TestCase
{
    public function testCanCreate(): void
    {
        $errorService = new SafeCacheErrorService();
        $this->assertInstanceOf(SafeCacheErrorService::class, $errorService);
    }

    public function testCanCreateInvalidArgumentError(): void
    {
        $errorService = new SafeCacheErrorService();
        $context = ErrorContext::create()->set('key', 'value');
        $mockInvalidArgumentException = $this->createMock(InvalidArgumentException::class);

        $error = $errorService->createForInvalidArgumentException($mockInvalidArgumentException, $context);
        $this->assertInstanceOf(KindError::class, $error);
        $this->assertEquals(SafeCacheErrorKind::InvalidArgument, $error->getKind());
        $this->assertTrue($error->hasException());
        $this->assertTrue($error->hasSpecificException(InvalidArgumentException::class));
        $this->assertSame('value', $error->getContext()->get('key'));
    }

    public function testCanCreateFalseReturnValueError(): void
    {
        $errorService = new SafeCacheErrorService();

        $context = ErrorContext::create()->set('key', 'value');

        $error = $errorService->createForFalseReturnValue($context);
        $this->assertInstanceOf(KindError::class, $error);
        $this->assertEquals(SafeCacheErrorKind::ReturnFalseError, $error->getKind());
        $this->assertEquals('value', $error->getContext()->get('key'));
    }

    public function testCanCreateCacheException(): void
    {
        $errorService = new SafeCacheErrorService();
        $context = ErrorContext::create()->set('key', 'value');
        $exception = $this->createMock(Throwable::class);

        $error = $errorService->createForCacheException($exception, $context);
        $this->assertInstanceOf(KindError::class, $error);

        $this->assertEquals(SafeCacheErrorKind::CacheException, $error->getKind());
        $this->assertTrue($error->hasException());
        $this->assertTrue($error->hasSpecificException(Throwable::class));
        $this->assertEquals('value', $error->getContext()->get('key'));
    }
}
