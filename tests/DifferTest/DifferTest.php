<?php

namespace DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testDiff(): void
    {
        $new = __DIR__ . '/../fixtures/new.json';
        $old = __DIR__ . '/../fixtures/old.json';
        $expected = file_get_contents(__DIR__ . '/../fixtures/expected.txt');
        $actual = genDiff($old, $new);
        $this->assertEquals($expected, $actual);
    }
}