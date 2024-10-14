<?php

declare(strict_types=1);

namespace SafeCache\Errors;

enum SafeCacheErrorKind
{
    case InvalidArgument;
    case ReturnFalseError;
    case CacheException;
}
