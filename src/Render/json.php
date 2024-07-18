<?php

namespace Differ\Render\Json;

function json(array $ast): string
{
    return json_encode($ast, JSON_PRETTY_PRINT);
}
