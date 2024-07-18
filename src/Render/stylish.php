<?php

namespace Differ\Render\Stylish;

const TAB_SPACE = '    ';
const ADDED = '  + ';
const DELETED = '  - ';
const UNMODIFIED = '    ';

function stylish($array): string
{
    $initialString = '{' . "\n";
    $bodyDiff =  getBody($array);
    $endString = "\n" . '}';
    return "{$initialString}{$bodyDiff}{$endString}";
}

function getBody($array, $depth = 0): string
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

function renderArray($array, $depth): string
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

function getIndent($depth): string
{
    $lengthIndent = strlen(TAB_SPACE) * $depth;
    return str_pad('', $lengthIndent, TAB_SPACE);
}

function renderNodesRemoved($data, $depth): string
{
    $prefix = getIndent($depth) . DELETED;
    $value = getValue($data['oldValue'], $depth);
    $view = "{$prefix}{$data['key']}: $value";
    return $view;
}

function renderNodesAdded($data, $depth): string
{
    $prefix = getIndent($depth) . ADDED;
    $value = getValue($data['newValue'], $depth);
    $view = "{$prefix}{$data['key']}: $value";
    return $view;
}

function renderNodesUnchanged($data, $depth): string
{
    $prefix = getIndent($depth) . UNMODIFIED;
    $value = getValue($data['newValue'], $depth);
    $view = "{$prefix}{$data['key']}: $value";
    return $view;
}

function renderNodesNested($data, $depth): string
{
    $prefix = getIndent($depth) . UNMODIFIED;
    $initialString = "{$prefix}{$data['key']}: {\n";
    $body = getBody($data['children'], $depth + 1);
    $endString = "\n" . getIndent($depth + 1) . "}";
    return "{$initialString}{$body}{$endString}";
}

function getValue($value, $depth)
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
