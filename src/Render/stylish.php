<?php

namespace Differ\Render\Stylish;

const TAB_SPACE = '    ';
const ADDED = '  + ';
const DELETED = '  - ';
const UNMODIFIED = '    ';

function stylish(array $array): string
{
    $initialString = '{' . "\n";
    $bodyDiff =  getBody($array);
    $endString = "\n" . '}';
    return "{$initialString}{$bodyDiff}{$endString}";
}

function getBody(array $array, int $depth = 0): string
{
    $bodyDiff = array_reduce($array, function ($acc, $data) use ($depth) {
        switch ($data['typeNode']) {
            case 'changed':
                $acc[] = renderNodesRemoved($data, $depth);
                $acc[] = renderNodesAdded($data, $depth);
                break;
            case 'unchanged':
                $acc[] = renderNodesUnchanged($data, $depth);
                break;
            case 'removed':
                $acc[] = renderNodesRemoved($data, $depth);
                break;
            case 'added':
                $acc[] = renderNodesAdded($data, $depth);
                break;
            case 'nested':
                $acc[] = renderNodesNested($data, $depth);
        }
        return $acc;
    }, []);
    return implode("\n", $bodyDiff);
}

function renderArray(array $array, int $depth): string
{
    $keys = array_keys($array);
    $viewArray = array_map(function ($key) use ($array, $depth) {
        $prefix = getIndent($depth) . UNMODIFIED;
        $value = getValue($array[$key], $depth);
        return "{$prefix}{$key}: $value";
    }, $keys);
    $initialString = "{\n";
    $endString = "\n" . getIndent($depth) . "}";
    $body = implode("\n", $viewArray);
    return "{$initialString}{$body}{$endString}";
}

function getIndent(int $depth): string
{
    $lengthIndent = strlen(TAB_SPACE) * $depth;
    return str_pad('', $lengthIndent, TAB_SPACE);
}

function renderNodesRemoved(mixed $data, int $depth): string
{
    $prefix = getIndent($depth) . DELETED;
    $value = getValue($data['oldValue'], $depth);
    return "{$prefix}{$data['key']}: $value";
}

function renderNodesAdded(mixed $data, int $depth): string
{
    $prefix = getIndent($depth) . ADDED;
    $value = getValue($data['newValue'], $depth);
    return "{$prefix}{$data['key']}: $value";
}

function renderNodesUnchanged(mixed $data, int $depth): string
{
    $prefix = getIndent($depth) . UNMODIFIED;
    $value = getValue($data['newValue'], $depth);
    return "{$prefix}{$data['key']}: $value";
}

function renderNodesNested(mixed $data, int $depth): string
{
    $prefix = getIndent($depth) . UNMODIFIED;
    $initialString = "{$prefix}{$data['key']}: {\n";
    $body = getBody($data['children'], $depth + 1);
    $endString = "\n" . getIndent($depth + 1) . "}";
    return "{$initialString}{$body}{$endString}";
}

function getValue(mixed $value, int $depth): mixed
{
    switch (gettype($value)) {
        case 'boolean':
            return $value ? 'true' : 'false';
        case 'array':
            return renderArray($value, $depth + 1);
        case 'NULL':
            return 'null';
        default:
            return $value;
    }
}
