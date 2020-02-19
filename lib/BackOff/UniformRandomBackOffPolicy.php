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
use function mt_rand;

/**
 * Implementation of {@link BackOffPolicyInterface} that pauses for a random period of time before continuing.
 */
class UniformRandomBackOffPolicy extends StatelessBackOffPolicy
{
    /**
     * Default min back off period (500ms).
     *
     * @var int
     */
    const DEFAULT_BACK_OFF_MIN_PERIOD = 500;

    /**
     * Default max back off period (1500ms).
     *
     * @var int
     */
    const DEFAULT_BACK_OFF_MAX_PERIOD = 1500;

    /**
     * The minimum back off period in milliseconds.
     *
     * @var int
     */
    private $minBackOffPeriod;

    /**
     * The maximum back off period in milliseconds.
     *
     * @var int
     */
    private $maxBackOffPeriod;

    /**
     * @var SleeperInterface
     */
    private $sleeper;

    /**ExponentialRandomBackOffPolicyTest.php
     *
     * @param int|null $minBackOffPeriod The minimum back off period in milliseconds.
     * @param int|null $maxBackOffPeriod The maximum back off period in milliseconds.
     */
    public function __construct(int $minBackOffPeriod = null, int $maxBackOffPeriod = null)
    {
        if ($minBackOffPeriod === null) {
            $minBackOffPeriod = self::DEFAULT_BACK_OFF_MIN_PERIOD;
        }

        if ($maxBackOffPeriod === null) {
            $maxBackOffPeriod = self::DEFAULT_BACK_OFF_MAX_PERIOD;
        }

        $this->setMinBackOffPeriod($minBackOffPeriod);
        $this->setMaxBackOffPeriod($maxBackOffPeriod);

        $this->sleeper = new DefaultSleeper();
    }

    /**
     * Set the minimum back off period in milliseconds. Cannot be &lt; 1. Default value is 500ms.
     */
    public function setMinBackOffPeriod(int $backOffPeriod)
    {
        $this->minBackOffPeriod = max(1, $backOffPeriod);
    }

    /**
     * The minimum back off period in milliseconds.
     */
    public function getMinBackOffPeriod(): int
    {
        return $this->minBackOffPeriod;
    }

    /**
     * Set the maximum back off period in milliseconds. Cannot be &lt; 1. Default value is 1500ms.
     */
    public function setMaxBackOffPeriod(int $backOffPeriod)
    {
        $this->maxBackOffPeriod = max(1, $backOffPeriod);
    }

    /**
     * The maximum back off period in milliseconds.
     */
    public function getMaxBackOffPeriod(): int
    {
        return $this->maxBackOffPeriod;
    }

    public function setSleeper(SleeperInterface $sleeper)
    {
        $this->sleeper = $sleeper;
    }

    protected function doBackOff()
    {
        if ($this->maxBackOffPeriod === $this->minBackOffPeriod) {
            $period = 0;
        } else {
            $period = mt_rand(0, $this->maxBackOffPeriod - $this->minBackOffPeriod);
        }

        $this->sleeper->sleep($this->minBackOffPeriod + $period);
    }
}
