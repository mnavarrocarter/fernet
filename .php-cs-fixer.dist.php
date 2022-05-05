<?php

$header = <<<EOF
@project MNC Fernet
@link https://github.com/mnavarrocarter/fernet
@project mnavarrocarter/fernet
@author Matias Navarro-Carter mnavarrocarter@gmail.com
@license MIT
@copyright 2022 Matias Navarro-Carter

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

return (new PhpCsFixer\Config())
    ->setCacheFile('var/php-cs-fixer.cache')
    ->setRiskyAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        'declare_strict_types' => true,
        'header_comment' => ['header' => $header, 'comment_type' => 'PHPDoc'],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests')
            ->exclude(__DIR__.'/var')
    );
