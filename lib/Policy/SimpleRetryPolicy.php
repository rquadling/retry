<?php

declare(strict_types=1);

/**
 * RQuadling/Retry
 *
 * LICENSE
 *
 * This is free and unencumbered software released into the public domain.
 *
 * Anyone is free to copy, modify, publish, use, compile, sell, or distribute this software, either in source code form or
 * as a compiled binary, for any purpose, commercial or non-commercial, and by any means.
 *
 * In jurisdictions that recognize copyright laws, the author or authors of this software dedicate any and all copyright
 * interest in the software to the public domain. We make this dedication for the benefit of the public at large and to the
 * detriment of our heirs and successors. We intend this dedication to be an overt act of relinquishment in perpetuity of
 * all present and future rights to this software under copyright law.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT
 * OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * For more information, please refer to <https://unlicense.org>
 *
 */

namespace RQuadling\Retry\Policy;

use RQuadling\Retry\RetryContextInterface;
use Throwable;
use function is_a;

/**
 * Simple retry policy that retries a fixed number of times for a set of named
 * exceptions (and subclasses). The number of attempts includes the initial try.
 */
class SimpleRetryPolicy extends AbstractRetryPolicy
{
    /**
     * The default limit to the number of attempts for a new policy.
     *
     * @var int
     */
    const DEFAULT_MAX_ATTEMPTS = 3;

    /**
     * The maximum number of retry attempts before failure.
     *
     * @var int
     */
    private $maxAttempts;

    /**
     * The list of retryable exceptions.
     *
     * @var array
     */
    private $retryableExceptions = ['Exception', 'ErrorExceptions'];

    /**
     * @param int|null $maxAttempts The number of attempts before a retry becomes impossible.
     */
    public function __construct(int $maxAttempts = null, array $retryableExceptions = null)
    {
        if ($maxAttempts === null) {
            $maxAttempts = self::DEFAULT_MAX_ATTEMPTS;
        }

        $this->maxAttempts = $maxAttempts;

        if ($retryableExceptions) {
            $this->retryableExceptions = $retryableExceptions;
        }
    }

    /**
     * The maximum number of retry attempts before failure.
     *
     * @return int The maximum number of attempts
     */
    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    /**
     * Setter for retry attempts.
     *
     * @param int $maxAttempts The number of attempts before a retry becomes impossible.
     *
     * @return void
     */
    public function setMaxAttempts(int $maxAttempts)
    {
        $this->maxAttempts = $maxAttempts;
    }

    /**
     * @return void
     */
    public function setRetryableExceptions(array $retryableExceptions)
    {
        $this->retryableExceptions = $retryableExceptions;
    }

    public function canRetry(RetryContextInterface $context): bool
    {
        $e = $context->getLastException();

        return (!$e || $this->shouldRetryForException($e)) && $context->getRetryCount() < $this->maxAttempts;
    }

    private function shouldRetryForException(Throwable $e): bool
    {
        foreach ($this->retryableExceptions as $class) {
            if (is_a($e, $class)) {
                return true;
            }
        }

        return false;
    }
}
