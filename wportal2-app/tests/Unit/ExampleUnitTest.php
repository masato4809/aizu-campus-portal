<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleUnitTest extends TestCase
{
    /**
     * 足し算と掛け算の組み合わせが期待通りかを検証するテスト.
     */
    public function test_simple_arithmetic(): void
    {
        $a = 3;
        $b = 4;
        $c = 2;

        $result = ($a + $b) * $c;

        $this->assertSame(14, $result);
    }
}
