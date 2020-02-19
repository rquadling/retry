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

namespace RQuadlingTests\Retry\BackOff;

use PHPUnit\Framework\TestCase;
use RQuadling\Retry\BackOff\BackOffContextInterface;
use RQuadling\Retry\BackOff\ExponentialRandomBackOffPolicy;
use RQuadlingTests\Retry\BackOff\Fixtures\DummySleeper;

class ExponentialRandomBackOffPolicyTest extends TestCase
{
    /**
     * @var ExponentialRandomBackOffPolicy
     */
    private $policy;

    /**
     * @var DummySleeper
     */
    private $sleeper;

    protected function setUp()
    {
        $this->policy = new ExponentialRandomBackOffPolicy();
        $this->sleeper = new DummySleeper();
        $this->policy->setSleeper($this->sleeper);
    }

    public function testSingleBackOff()
    {
        $seed = $this->policy->getInitialInterval();
        $multiplier = $this->policy->getMultiplier();

        $context = $this->policy->start();
        $this->backOff($context);

        $this->assertEquals($this->randomize($seed, $multiplier), $this->sleeper->getLastBackOff());
    }

    public function testMaximumBackOff()
    {
        $maxInterval = 50;
        $multiplier = $this->policy->getMultiplier();

        $this->policy->setMaxInterval($maxInterval);

        $context = $this->policy->start();
        $this->backOff($context);

        $this->assertEquals($this->randomize($maxInterval, $multiplier), $this->sleeper->getLastBackOff());
    }

    public function testMultiBackOff()
    {
        $seed = 40;
        $multiplier = 1.2;

        $this->policy->setInitialInterval($seed);
        $this->policy->setMultiplier($multiplier);

        $context = $this->policy->start();

        for ($x = 0; $x < 5; ++$x) {
            $this->backOff($context);
            $this->assertEquals($this->randomize($seed, $multiplier), $this->sleeper->getLastBackOff());

            $seed = (int)($seed * $multiplier);
        }
    }

    private function backOff(BackOffContextInterface $context)
    {
        \mt_srand(1);
        $this->policy->backOff($context);
    }

    private function randomize(int $seed, float $multiplier): int
    {
        \mt_srand(1);
        $random = \mt_rand(0, \mt_getrandmax()) / \mt_getrandmax();

        return (int)($seed * (1 + $random * ($multiplier - 1)));
    }
}
