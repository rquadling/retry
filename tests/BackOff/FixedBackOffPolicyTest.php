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
use RQuadling\Retry\BackOff\FixedBackOffPolicy;
use RQuadlingTests\Retry\BackOff\Fixtures\DummySleeper;

class FixedBackOffPolicyTest extends TestCase
{
    /**
     * @var FixedBackOffPolicy
     */
    private $policy;

    /**
     * @var DummySleeper
     */
    private $sleeper;

    protected function setUp()
    {
        $this->policy = new FixedBackOffPolicy();
        $this->sleeper = new DummySleeper();
        $this->policy->setSleeper($this->sleeper);
    }

    public function testSetBackOffPeriodNegative()
    {
        $this->policy->setBackOffPeriod(-1000);
        $this->policy->backOff();

        $this->assertEquals(1, \count($this->sleeper->getBackOffs()));
        $this->assertEquals(1, $this->sleeper->getLastBackOff());
    }

    public function testSingleBackOff()
    {
        $this->policy->setBackOffPeriod(50);
        $this->policy->backOff();

        $this->assertEquals(1, \count($this->sleeper->getBackOffs()));
        $this->assertEquals(50, $this->sleeper->getLastBackOff());
    }

    public function testManyBackOffCalls()
    {
        $this->policy->setBackOffPeriod(50);
        $this->policy->start();

        for ($x = 0; $x < 10; ++$x) {
            $this->policy->backOff();
            $this->assertEquals(50, $this->sleeper->getLastBackOff());
        }

        $this->assertEquals(10, \count($this->sleeper->getBackOffs()));
    }
}
