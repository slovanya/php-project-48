<?php

namespace Differ\Render;

use function Differ\Render\Stylish\stylish;

function render($arr, $format)
{
    $formats = [
        'stylish' => function ($ast) {
            return stylish($ast);
        }
    ];
    return $formats[$format]($arr);
}