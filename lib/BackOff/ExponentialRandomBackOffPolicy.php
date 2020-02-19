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

namespace RQuadling\Retry\BackOff;

use RQuadling\Retry\RetryContextInterface;

/**
 * Implementation of {@link ExponentialBackOffPolicy} that chooses a random multiple of the interval.
 * The random multiple is selected based on how many iterations have occurred.
 *
 * Example:
 *   initialInterval = 50
 *   multiplier      = 2.0
 *   maxInterval     = 3000
 *
 * {@link ExponentialBackOffPolicy} yields:           [50, 100, 200, 400, 800]
 *
 * {@link ExponentialRandomBackOffPolicy} may yield   [50, 100, 100, 100, 600]
 *                                               or   [50, 100, 150, 400, 800]
 */
class ExponentialRandomBackOffPolicy extends ExponentialBackOffPolicy
{
    public function start(RetryContextInterface $context = null): BackOffContextInterface
    {
        return new ExponentialRandomBackOffContext(
            $this->getInitialInterval(),
            $this->getMultiplier(),
            $this->getMaxInterval()
        );
    }
}
