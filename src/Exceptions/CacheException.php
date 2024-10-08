<?php

declare(strict_types=1);

namespace SafeCache\Exceptions;

use Exception;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;
use Zodimo\BaseReturn\Option;

class CacheException extends Exception implements Throwable
{
    private const TAG_INVALID_ARGUMENT = 'invalidArgument';

    private const TAG_UNSPECIFIED_ERROR = 'unspecified';

    private const TAG_UNSPECIFIED_EXCEPTION = 'unspecified';

    /**
     * @var Option<Throwable>
     */
    private Option $exceptionOption;

    private string $tag;

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function createForInvalidArgument(InvalidArgumentException $exception): CacheException
    {
        $self = new self($exception->getMessage());
        $self->exceptionOption = Option::some($exception);
        $self->tag = self::TAG_INVALID_ARGUMENT;

        return $self;
    }

    public static function createForUnspecifiedException(Throwable $exception): CacheException
    {
        $self = new self($exception->getMessage());
        $self->exceptionOption = Option::some($exception);
        $self->tag = self::TAG_UNSPECIFIED_EXCEPTION;

        return $self;
    }

    public static function createForUnspecifiedError(): CacheException
    {
        $self = new self('Cache returned false');
        $self->exceptionOption = Option::none();
        $self->tag = self::TAG_UNSPECIFIED_ERROR;

        return $self;
    }

    /**
     * @return Option<Throwable>
     */
    public function getException(): Option
    {
        return $this->exceptionOption;
    }

    /**
     * @phpstan-assert-if-true Option<InvalidArgumentException> $this->getException();
     *
     * @phpstan-assert-if-false Option<mixed> $this->getException();
     */
    public function isInvalidArgumentException(): bool
    {
        return self::TAG_INVALID_ARGUMENT === $this->tag;
    }

    /**
     * @phpstan-assert-if-true Option<Throwable> $this->getException();
     *
     * @phpstan-assert-if-false Option<mixed> $this->getException();
     */
    public function isUnspecifiedExpection(): bool
    {
        return self::TAG_UNSPECIFIED_EXCEPTION === $this->tag;
    }
}
