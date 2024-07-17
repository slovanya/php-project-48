<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;
use function Differ\Parsers\parseJson;
use function Differ\Parsers\parseYaml;

function stringify(mixed $key, mixed $value): string
{
    $string = '';
    if ($value === false) {
        $string .= "{$key}: false\n";
    } elseif ($value === true) {
        $string .= "{$key}: true\n";
    } else {
        $string .= "{$key}: {$value}\n";
    }
    return $string;
}

/**
 * @throws \Exception
 */
function genDiff(string $path1, string $path2): string
{
    $path1Ext = pathinfo($path1, PATHINFO_EXTENSION);
    $path2Ext = pathinfo($path2, PATHINFO_EXTENSION);
    if ($path1Ext !== $path2Ext) {
        throw new \Exception('Wrong format');
    } elseif ($path1Ext === 'json') {
        $decFile1 = parseJson($path1);
        $decFile2 = parseJson($path2);
    } else {
        $decFile1 = parseYaml($path1);
        $decFile2 = parseYaml($path2);
    }

    $merged = array_merge($decFile1, $decFile2);
    $keys = array_keys($merged);
    sort($keys);
    $keys1 = array_keys($decFile1);
    $keys2 = array_keys($decFile2);

    $string = "{\n";
    foreach ($keys as $key) {
        if (in_array($key, $keys1) && in_array($key, $keys2)) {
            if ($decFile1[$key] !== $decFile2[$key]) {
                $string .= '  - ' . stringify($key, $decFile1[$key]);
                $string .= '  + ' . stringify($key, $decFile2[$key]);
            } else {
                $string .= '    ' . stringify($key, $merged[$key]);
            }
        } elseif (in_array($key, $keys1)) {
            $string .= '  - ' . stringify($key, $decFile1[$key]);
        } else {
            $string .= '  + ' . stringify($key, $decFile2[$key]);
        }
    }
    $string .= "}";
    return $string;
}
