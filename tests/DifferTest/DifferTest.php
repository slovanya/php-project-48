<?php

namespace DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testDiffJson(): void
    {
        $new = __DIR__ . '/../fixtures/new.json';
        $old = __DIR__ . '/../fixtures/old.json';
        $expected = file_get_contents(__DIR__ . '/../fixtures/expected.txt');
        $actual = genDiff($old, $new);
        $this->assertEquals($expected, $actual);
    }

    public function testDiffYaml(): void
    {
        $new = __DIR__ . '/../fixtures/new.yaml';
        $old = __DIR__ . '/../fixtures/old.yaml';
        $expected = file_get_contents(__DIR__ . '/../fixtures/expected.txt');
        $actual = genDiff($old, $new);
        $this->assertEquals($expected, $actual);
    }
    public function testDiffNested(): void
    {
        $new = __DIR__ . '/../fixtures/new2.json';
        $old = __DIR__ . '/../fixtures/old2.json';
        $expected = file_get_contents(__DIR__ . '/../fixtures/expected2.txt');
        $actual = genDiff($old, $new);
        $this->assertEquals($expected, $actual);
    }
    public function testDiffPlain(): void
    {
        $new = __DIR__ . '/../fixtures/new2.json';
        $old = __DIR__ . '/../fixtures/old2.json';
        $result2plain = genDiff($old, $new, "plain");
        $expected2plain = file_get_contents(__DIR__ . "/../fixtures/expected2plain.txt");
        $this->assertEquals($expected2plain, $result2plain);
    }
}
