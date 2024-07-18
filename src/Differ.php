<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\Render\render;
use function Functional\sort;

function genDiff(string $firstFilePath, string $secondFilePath, string $format = "stylish"): string
{
    $firstArray = parse($firstFilePath);
    $secondArray = parse($secondFilePath);
    $diff = makeDiff($firstArray, $secondArray);
    return render($diff, $format);
}

function makeDiff(array $before, array $after): array
{
    $keys = array_unique(array_merge(array_keys($before), array_keys($after)));
    $unionKeys = sort($keys, fn($left, $right) => strcmp($left, $right));
    return array_map(function ($key) use ($before, $after) {
        $node = [];
        if (array_key_exists($key, $before) && array_key_exists($key, $after)) {
            if (is_array($before[$key]) && is_array($after[$key])) {
                $node = buildNode('nested', $key, null, null, makeDiff($before[$key], $after[$key]));
            } elseif ($before[$key] === $after[$key]) {
                $node = buildNode("unchanged", $key, $before[$key], $after[$key]);
            } else {
                $node = buildNode("changed", $key, $before[$key], $after[$key]);
            }
        }
        if (array_key_exists($key, $before) && !array_key_exists($key, $after)) {
            $node = buildNode("removed", $key, $before[$key], null);
        }
        if (!array_key_exists($key, $before) && array_key_exists($key, $after)) {
            $node = buildNode("added", $key, null, $after[$key]);
        }

        return $node;
    }, $unionKeys);
}

function buildNode(mixed $typeNode, mixed $key, mixed $oldValue, mixed $newValue, mixed $children = null): array
{
    return [
        'typeNode' => $typeNode,
        'key' => $key,
        'oldValue' => $oldValue,
        'newValue' => $newValue,
        'children' => $children
    ];
}
