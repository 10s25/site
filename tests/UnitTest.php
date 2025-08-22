<?php
declare(strict_types=1);

/**
 * This file is part of the winlassie-io Application.
 *
 * (c) GammaSoftware <https://www.winlassie.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
abstract class UnitTest
{
    abstract public function run(): void;
    public function printResult(string $testName, bool $passed): void
    {
        print_r(value: $passed ?
            sprintf(
                "\033[34m\u{2713} Test %s\033[0m \033[32mpassed\033[0m\n",
                $testName
            ) : sprintf(
                "\033[34m\u{2717} Test %s\033[0m \033[31mfailed\033[0m\n",
                $testName
            )
        );
    }
}
