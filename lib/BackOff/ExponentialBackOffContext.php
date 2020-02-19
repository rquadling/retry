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

use function max;

class ExponentialBackOffContext implements BackOffContextInterface
{
    /** @var int */
    private $seed;

    /** @var float */
    private $multiplier;

    /** @var int */
    private $max;

    /** @var int */
    private $interval;

    public function __construct(int $seed, float $multiplier, int $max)
    {
        $this->seed = max(1, $seed);
        $this->multiplier = max(1, $multiplier);
        $this->max = max(1, $max);

        $this->interval = $this->seed;
    }

    public function getIntervalAndIncrement(): int
    {
        $interval = $this->interval;

        if ($interval > $this->max) {
            $interval = $this->max;
        } else {
            $this->interval = $this->getNextInterval();
        }

        return $interval;
    }

    public function getInterval(): int
    {
        return $this->interval;
    }

    public function resetInterval()
    {
        $this->interval = $this->seed;
    }

    public function getNextInterval(): int
    {
        return (int) ($this->interval * $this->multiplier);
    }

    public function getMultiplier(): float
    {
        return $this->multiplier;
    }
}
