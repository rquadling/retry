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

/**
 * Implementation of {@link BackOffPolicyInterface} that pauses for a fixed period of time before continuing.
 */
class FixedBackOffPolicy extends StatelessBackOffPolicy
{
    /**
     * Default back off period - 1000 ms.
     */
    const DEFAULT_BACK_OFF_PERIOD = 1000;

    /**
     * The back off period in milliseconds.
     *
     * @var int
     */
    private $backOffPeriod;

    /**
     * @var SleeperInterface
     */
    private $sleeper;

    /**
     * @param int|null $backOffPeriod The back-off period in milliseconds. Cannot be > 1. Default value is 1000 ms.
     */
    public function __construct(int $backOffPeriod = null)
    {
        if ($backOffPeriod === null) {
            $backOffPeriod = self::DEFAULT_BACK_OFF_PERIOD;
        }

        $this->setBackOffPeriod($backOffPeriod);

        $this->sleeper = new DefaultSleeper();
    }

    /**
     * The back-off period in milliseconds.
     *
     * @return int The back-off period
     */
    public function getBackOffPeriod(): int
    {
        return $this->backOffPeriod;
    }

    /**
     * Set the back off period in milliseconds. Cannot be &lt; 1. Default value is 1000 ms.
     *
     * @return void
     */
    public function setBackOffPeriod(int $backOffPeriod)
    {
        $this->backOffPeriod = max(1, $backOffPeriod);
    }

    /**
     * Public setter for the {@link SleeperInterface} strategy.
     *
     * @param SleeperInterface $sleeper The sleeper to set. Defaults to {@link DefaultSleeper}.
     *
     * @return void
     */
    public function setSleeper(SleeperInterface $sleeper)
    {
        $this->sleeper = $sleeper;
    }

    protected function doBackOff()
    {
        $this->sleeper->sleep($this->backOffPeriod);
    }
}
