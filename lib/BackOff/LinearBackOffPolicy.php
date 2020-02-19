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

use InvalidArgumentException;
use RQuadling\Retry\RetryContextInterface;
use function max;

/**
 * Implementation of {@link BackOffPolicyInterface} that linearly increases the back-off period for each retry attempt.
 */
class LinearBackOffPolicy extends AbstractBackOffPolicy
{
    /**
     * The default initial interval value - 100 ms.
     *
     * @var int
     */
    const DEFAULT_INITIAL_INTERVAL = 1000;

    /**
     * The default maximum back-off time (30 seconds).
     *
     * @var int
     */
    const DEFAULT_MAX_INTERVAL = 30000;

    /**
     * The default delta value (1 second).
     *
     * @var float
     */
    const DEFAULT_DELTA_INTERVAL = 1000;

    /**
     * The initial sleep interval.
     *
     * @var int
     */
    private $initialInterval;

    /**
     * The maximum value of the back-off period in milliseconds.
     *
     * @var int
     */
    private $maxInterval;

    /**
     * The value to linearly increment the seed with for each retry attempt.
     *
     * @var float
     */
    private $deltaInterval;

    /**
     * @var SleeperInterface
     */
    private $sleeper;

    /**
     * @param int|null $initialInterval The initial sleep interval value. Default is 100 ms.
     *                                  Cannot be set to a value less than one.
     * @param float|null $deltaInterval The delta value. Default is 1000.
     * @param int|null $maxInterval The maximum back off period. Default is 30000 (30 seconds).
     *                              The value will be reset to 1 if this method is called with a value less than 1.
     *                              Set this to avoid infinite waits if backing-off a large number of times.
     */
    public function __construct(int $initialInterval = null, float $deltaInterval = null, int $maxInterval = null)
    {
        if ($initialInterval === null) {
            $initialInterval = self::DEFAULT_INITIAL_INTERVAL;
        }

        if ($deltaInterval === null) {
            $deltaInterval = self::DEFAULT_DELTA_INTERVAL;
        }

        if ($maxInterval === null) {
            $maxInterval = self::DEFAULT_MAX_INTERVAL;
        }

        $this->setInitialInterval($initialInterval);
        $this->setDeltaInterval($deltaInterval);
        $this->setMaxInterval($maxInterval);

        $this->sleeper = new DefaultSleeper();
    }

    /**
     * The initial period to sleep on the first back-off.
     *
     * @return int The initial interval
     */
    public function getInitialInterval(): int
    {
        return $this->initialInterval;
    }

    /**
     * Set the initial sleep interval value. Default is 1000 millisecond.
     * Cannot be set to a value less than one.
     *
     * @return void
     */
    public function setInitialInterval(int $initialInterval)
    {
        $this->initialInterval = max(1, $initialInterval);
    }

    /**
     * The delta to use to generate the next back-off interval from the last.
     *
     * @return int The delta in use
     */
    public function getDeltaInterval(): int
    {
        return (int) $this->deltaInterval;
    }

    /**
     * Set the delta interval value. Default is 1000.
     *
     * @return void
     */
    public function setDeltaInterval(float $delta)
    {
        $this->deltaInterval = max(1, (int) $delta);
    }

    /**
     * The maximum interval to sleep for. Defaults to 30 seconds.
     *
     * @return int the maximum interval
     */
    public function getMaxInterval(): int
    {
        return $this->maxInterval;
    }

    /**
     * Setter for maximum back-off period. Default is 30000 (30 seconds).
     * The value will be reset to 1 if this method is called with a value less than 1.
     * Set this to avoid infinite waits if backing off a large number of times.
     *
     * @return void
     */
    public function setMaxInterval(int $maxInterval)
    {
        $this->maxInterval = max(1, $maxInterval);
    }

    public function setSleeper(SleeperInterface $sleeper)
    {
        $this->sleeper = $sleeper;
    }

    public function start(RetryContextInterface $context = null): BackOffContextInterface
    {
        return new LinearBackOffContext($this->initialInterval, $this->getDeltaInterval(), $this->maxInterval);
    }

    /**
     * @param BackOffContextInterface|LinearBackOffContext|null $context
     */
    public function backOff(BackOffContextInterface $context = null)
    {
        if (!$context instanceof LinearBackOffContext) {
            throw new InvalidArgumentException('Context is expected to be an instanceof LinearBackOffContext.');
        }

        $this->sleeper->sleep($context->getIntervalAndIncrement());
    }
}
