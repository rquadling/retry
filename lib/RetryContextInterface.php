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

namespace RQuadling\Retry;

use Throwable;

/**
 * Low-level access to ongoing retry operation.
 */
interface RetryContextInterface
{
    /**
     * Counts the number of retry attempts.
     */
    public function getRetryCount(): int;

    /**
     * Set the exception for the public interface and increment retries counter.
     *
     * All {@link RetryPolicyInterface} implementations should use this method when they register the exception.
     * It should only be called once per retry attempt because it increments a counter.
     *
     * @param Throwable $exception The exception that caused the current retry attempt to fail.
     *
     * @return void
     */
    public function registerException(Throwable $exception);

    /**
     * Accessor for the exception object that caused the current retry.
     *
     * @return Throwable|null The last exception that caused a retry, or possibly null.
     */
    public function getLastException();
}
