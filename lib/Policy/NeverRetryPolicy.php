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

/**
 * A {@link RetryPolicyInterface} that allows the first attempt but never permits a retry.
 * Also be used as a base class for other policies, e.g. for test purposes as a stub.
 */
class NeverRetryPolicy extends AbstractRetryPolicy
{
    public function open(): RetryContextInterface
    {
        return new NeverRetryContext();
    }

    public function canRetry(RetryContextInterface $context): bool
    {
        $context = NeverRetryContext::cast($context);

        return !$context->isFinished();
    }

    public function registerException(RetryContextInterface $context, Throwable $exception)
    {
        $context = NeverRetryContext::cast($context);
        $context->setFinished();

        parent::registerException($context, $exception);
    }
}
