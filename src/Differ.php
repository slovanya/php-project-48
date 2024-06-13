<?php

namespace Differ\Differ;

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

function genDiff(string $path1, string $path2): string
{
    $decJson1 = json_decode(file_get_contents($path1), true);
    $decJson2 = json_decode(file_get_contents($path2), true);
    $merged = array_merge($decJson1, $decJson2);
    $keys = array_keys($merged);
    sort($keys);
    $keys1 = array_keys($decJson1);
    $keys2 = array_keys($decJson2);

    $string = "{\n";
    foreach ($keys as $key) {
        if (in_array($key, $keys1) && in_array($key, $keys2)) {
            if ($decJson1[$key] !== $decJson2[$key]) {
                $string .= '  - ' . stringify($key, $decJson1[$key]);
                $string .= '  + ' . stringify($key, $decJson2[$key]);
            } else {
                $string .= '    ' . stringify($key, $merged[$key]);
            }
        } elseif (in_array($key, $keys1)) {
            $string .= '  - ' . stringify($key, $decJson1[$key]);
        } else {
            $string .= '  + ' . stringify($key, $decJson2[$key]);
        }
    }
    $string .= "}";
    return $string;
}
