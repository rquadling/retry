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

/**
 * A {@link RetryPolicyInterface} that allows a retry only if it has not timed out.
 */
class TimeoutRetryPolicy extends AbstractRetryPolicy
{
    /**
     * Default value for timeout (milliseconds).
     *
     * @var int
     */
    const DEFAULT_TIMEOUT = 1000;

    /**
     * The value of the timeout.
     *
     * @var int
     */
    private $timeout;

    /**
     * @param int|null $timeout The timeout in milliseconds. Default is 1000 ms.
     */
    public function __construct(int $timeout = null)
    {
        if ($timeout === null) {
            $timeout = self::DEFAULT_TIMEOUT;
        }

        $this->setTimeout($timeout);
    }

    /**
     * The value of the timeout.
     *
     * @return int The timeout in milliseconds
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Setter for timeout in milliseconds. Default is 1000 ms.
     *
     * @return void
     */
    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;
    }

    public function open(): RetryContextInterface
    {
        return new TimeoutRetryContext($this->timeout);
    }

    public function canRetry(RetryContextInterface $context): bool
    {
        $context = TimeoutRetryContext::cast($context);

        return $context->isAlive();
    }
}
