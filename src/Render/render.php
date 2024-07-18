<?php

namespace Differ\Render;

use function Differ\Render\Stylish\stylish;
use function Differ\Render\Plain\plain;

function render($arr, $format)
{
    $formats = [
        'stylish' => function ($ast) {
            return stylish($ast);
        },
        'plain' => function ($ast) {
            return plain($ast);
        }
    ];
    return $formats[$format]($arr);
}