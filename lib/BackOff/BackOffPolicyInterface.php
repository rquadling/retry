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
 * Strategy interface to control back off between attempts in a single retry operation.
 *
 * For each block of retry operations the {@link start} method is called and implementations can return
 * an implementation-specific {@link BackOffContextInterface} that can be used to track state through subsequent
 * back off invocations.
 *
 * Each back off process is handled via a call to {@link backOff} method.
 * The {@link RetryProxy} will pass in the corresponding {@link BackOffContextInterface} object created by the call to
 * {@link start}.
 */
interface BackOffPolicyInterface
{
    /**
     * Start a new block of back off operations. Implementations can choose to
     * pause when this method is called, but normally it returns immediately.
     *
     * @param RetryContextInterface|null $context The current retry context, which might contain information
     *                                            that we can use to decide how to proceed.
     *
     * @return BackOffContextInterface|null
     */
    public function start(RetryContextInterface $context = null);

    /**
     * Back-off/pause in an implementation-specific fashion. The passed in
     * {@link BackOffContextInterface} corresponds to the one created by the call to
     * {@link start} method for a given retry operation set.
     *
     * @return void
     */
    public function backOff(BackOffContextInterface $context = null);
}
