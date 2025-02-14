<?php

declare(strict_types=1);

namespace SafeCache;

use KindErrors\ErrorContext;
use KindErrors\KindError;
use Psr\SimpleCache\InvalidArgumentException;
use SafeCache\Errors\SafeCacheErrorKind;
use Throwable;

class SafeCacheErrorService
{
    /**
     * @return KindError<SafeCacheErrorKind>
     */
    public function createForFalseReturnValue(ErrorContext $context): KindError
    {
        $message = 'Cache returned false';

        return KindError::create(SafeCacheErrorKind::ReturnFalseError, $message, $context);
    }

    /**
     * @return KindError<SafeCacheErrorKind>
     */
    public function createForCacheException(Throwable $exception, ErrorContext $context): KindError
    {
        $message = "Cache Exception: {$exception->getMessage()}";
        if (!$context->hasException()) {
            $context->setException($exception);
        }

        return KindError::create(SafeCacheErrorKind::CacheException, $message, $context);
    }

    /**
     * @return KindError<SafeCacheErrorKind>
     */
    public function createForInvalidArgumentException(InvalidArgumentException $exception, ErrorContext $context): KindError
    {
        $message = "InvalidArgumentException: {$exception->getMessage()}";
        if (!$context->hasException()) {
            $context->setException($exception);
        }

        return KindError::create(SafeCacheErrorKind::InvalidArgument, $message, $context);
    }
}
