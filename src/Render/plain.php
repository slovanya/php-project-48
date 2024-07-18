<?php

namespace Differ\Render\Plain;

use function Functional\flatten;

const ADDED = "Property '%s' was added with value: %s";
const REMOVED = "Property '%s' was removed";
const CHANGED = "Property '%s' was updated. From %s to %s";
const VALUE_IS_ARRAY = "[complex value]";

function plain(array $ast): string
{
    return implode("\n", array_filter(flatten(array_map(function ($item) {
        return getPlain($item, '');
    }, $ast)), fn($item) => $item !== ''));
}

function getPlain(mixed $item, mixed $path): string|array
{
    [
        'typeNode' => $type,
        'key' => $key,
        'oldValue' => $before,
        'newValue' => $after,
        'children' => $children
    ] = $item;

    $beforeV = getValue($before);
    $afterV = getValue($after);
    $name = "{$path}{$key}";
    $nameForChildren = "{$path}{$key}.";
    switch ($type) {
        case 'nested':
            return array_map(function ($item) use ($nameForChildren) {
                return getPlain($item, $nameForChildren);
            }, $children);

        case 'changed':
            return sprintf(CHANGED, $name, $beforeV, $afterV);

        case 'removed':
            return sprintf(REMOVED, $name);

        case 'added':
            return sprintf(ADDED, $name, $afterV);
        default:
            return '';
    }
}

function getValue(mixed $value): mixed
{
    switch (gettype($value)) {
        case 'boolean':
            return $value ? 'true' : 'false';
        case 'array':
            return VALUE_IS_ARRAY;
        case 'NULL':
            return 'null';
        case 'string':
            return "'" . $value . "'";
        default:
            return $value;
    }
}
