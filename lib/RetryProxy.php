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

namespace RQuadling\Retry;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RQuadling\Retry\BackOff\BackOffPolicyInterface;
use RQuadling\Retry\BackOff\ExponentialBackOffPolicy;
use RQuadling\Retry\Policy\RetryPolicyInterface;
use RQuadling\Retry\Policy\SimpleRetryPolicy;
use Throwable;
use function call_user_func_array;
use function sprintf;

class RetryProxy implements RetryProxyInterface
{
    /**
     * @var RetryPolicyInterface
     */
    private $retryPolicy;

    /**
     * @var BackOffPolicyInterface
     */
    private $backOffPolicy;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var int */
    private $tryCount;

    public function __construct(
        RetryPolicyInterface $retryPolicy = null,
        BackOffPolicyInterface $backOffPolicy = null,
        LoggerInterface $logger = null
    ) {
        if ($retryPolicy === null) {
            $retryPolicy = new SimpleRetryPolicy();
        }

        if ($backOffPolicy === null) {
            $backOffPolicy = new ExponentialBackOffPolicy();
        }

        if ($logger === null) {
            $logger = new NullLogger();
        }

        $this->retryPolicy = $retryPolicy;
        $this->backOffPolicy = $backOffPolicy;
        $this->logger = $logger;
    }

    /**
     * Executing the action until it either succeeds or the policy dictates that we stop,
     * in which case the most recent exception thrown by the action will be rethrown.
     *
     * @return mixed
     *
     * @throws TerminatedRetryException
     */
    public function call(callable $action, array $arguments = [])
    {
        $retryContext = $this->retryPolicy->open();
        $backOffContext = $this->backOffPolicy->start($retryContext);
        $this->tryCount = 0;

        while ($this->retryPolicy->canRetry($retryContext)) {
            try {
                ++$this->tryCount;

                return call_user_func_array($action, $arguments);
            } catch (Throwable $thrownException) {
                try {
                    $this->retryPolicy->registerException($retryContext, $thrownException);
                } catch (Throwable $policyException) {
                    throw new TerminatedRetryException('Terminated retry after error in policy.');
                }
            }

            if ($this->retryPolicy->canRetry($retryContext)) {
                $this->logger->info(
                    sprintf(
                        '%s. Retrying... [%dx]',
                        $thrownException->getMessage(),
                        $retryContext->getRetryCount()
                    )
                );
                $this->backOffPolicy->backOff($backOffContext);
            }
        }

        $lastException = $retryContext->getLastException();
        if ($lastException) {
            throw $lastException;
        }

        throw new RetryException('Action call is failed.');
    }

    public function getTryCount(): int
    {
        return $this->tryCount;
    }
}
