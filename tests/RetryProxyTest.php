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

namespace RQuadlingTests\Retry;

use PHPUnit\Framework\TestCase;
use RQuadling\Retry\BackOff\NoBackOffPolicy;
use RQuadling\Retry\Policy\NeverRetryPolicy;
use RQuadling\Retry\Policy\SimpleRetryPolicy;
use RQuadling\Retry\RetryProxy;
use RQuadlingTests\Retry\Fixtures\MockBackOffStrategy;
use RQuadlingTests\Retry\Fixtures\MockRetryClass;

class RetryProxyTest extends TestCase
{
    public function testSuccessfulRetry()
    {
        for ($x = 1; $x <= 10; ++$x) {
            $action = new MockRetryClass($x);
            $proxy = new RetryProxy(new SimpleRetryPolicy($x), new NoBackOffPolicy());
            $proxy->call([$action, 'action']);
            $this->assertEquals($x, $action->attempts);
        }
    }

    public function testAlwaysTryAtLeastOnce()
    {
        $action = new MockRetryClass(1);
        $proxy = new RetryProxy(new NeverRetryPolicy());
        $proxy->call([$action, 'action']);
        $this->assertEquals(1, $action->attempts);
    }

    public function testNoSuccessRetry()
    {
        $action = new MockRetryClass(PHP_INT_MAX, new \InvalidArgumentException());
        $proxy = new RetryProxy(new SimpleRetryPolicy(2));
        try {
            $proxy->call([$action, 'action']);
            $this->fail('Expected InvalidArgumentException.');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(2, $action->attempts);

            return;
        }
        $this->fail('Expected InvalidArgumentException.');
    }

    public function testSetExceptions()
    {
        $action = new MockRetryClass(3);
        $proxy = new RetryProxy(new SimpleRetryPolicy(3, ['RuntimeException']));
        try {
            $proxy->call([$action, 'action']);
        } catch (\Throwable $e) {
            $this->assertEquals(1, $action->attempts);
        }
        $action->exceptionToThrow = new \RuntimeException();
        $proxy->call([$action, 'action']);
        $this->assertEquals(3, $action->attempts);
    }

    public function testErrorExceptions()
    {
        $action = new MockRetryClass(3);
        $proxy = new RetryProxy(new SimpleRetryPolicy());
        try {
            $proxy->call([$action, 'action']);
        } catch (\Throwable $e) {
            $this->assertEquals(1, $action->attempts);
        }
        $action->exceptionToThrow = new \ErrorException();
        $proxy->call([$action, 'action']);
        $this->assertEquals(4, $action->attempts);
    }

    public function testBackOffInvoked()
    {
        for ($x = 1; $x <= 10; ++$x) {
            $action = new MockRetryClass($x);
            $backOff = new MockBackOffStrategy();
            $proxy = new RetryProxy(new SimpleRetryPolicy($x), $backOff);
            $proxy->call([$action, 'action']);
            $this->assertEquals($x, $action->attempts);
            $this->assertEquals(1, $backOff->initCalls);
            $this->assertEquals($x - 1, $backOff->backOffCalls);
        }
    }

    public function testRethrowError()
    {
        $proxy = new RetryProxy(new NeverRetryPolicy());
        try {
            $proxy->call(function () {
                throw new \ErrorException('Realllly bad!');
            });
            $this->fail('Expected Error');
        } catch (\ErrorException $e) {
            $this->assertEquals('Realllly bad!', $e->getMessage());
        }
    }

    public function testTryCount()
    {
        $action = new MockRetryClass(5);
        $proxy = new RetryProxy(new SimpleRetryPolicy(5, ['RuntimeException']));
        try {
            $proxy->call([$action, 'action']);
        } catch (\Throwable $e) {
            $this->assertEquals(1, $action->attempts);
            $this->assertEquals($proxy->getTryCount(), $action->attempts);
        }
        $action->exceptionToThrow = new \RuntimeException();
        $proxy->call([$action, 'action']);
        $this->assertEquals(4, $proxy->getTryCount());
    }
}
