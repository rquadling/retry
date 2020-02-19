<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

$header = <<<TXT
RQuadling/Retry

LICENSE

This is free and unencumbered software released into the public domain.

Anyone is free to copy, modify, publish, use, compile, sell, or distribute this software, either in source code form or
as a compiled binary, for any purpose, commercial or non-commercial, and by any means.

In jurisdictions that recognize copyright laws, the author or authors of this software dedicate any and all copyright
interest in the software to the public domain. We make this dedication for the benefit of the public at large and to the
detriment of our heirs and successors. We intend this dedication to be an overt act of relinquishment in perpetuity of
all present and future rights to this software under copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT
OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

For more information, please refer to <https://unlicense.org>

TXT;

$rules = [
    '@PSR2' => true,
    '@Symfony' => true,
    'cast_spaces' => [
        'space' => 'none',
    ],
    'concat_space' => [
        'spacing' => 'none',
    ],
    'native_function_invocation' => [
        'scope' => 'namespaced',
    ],
    'psr4' => true,
    'phpdoc_align' => [
        'align' => 'left',
    ],
    'array_syntax' => [
        'syntax' => 'short',
    ],
    'header_comment' => [
        'header' => $header,
        'commentType' => PhpCsFixer\Fixer\Comment\HeaderCommentFixer::HEADER_PHPDOC,
    ],
    'yoda_style' => false,
];

$cacheDir = getenv('TRAVIS') ? getenv('HOME') . '/.php-cs-fixer' : __DIR__;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules($rules)
    ->setFinder($finder)
    ->setCacheFile($cacheDir . '/.php_cs.cache');
