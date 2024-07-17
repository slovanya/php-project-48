<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseJson(string $path): array
{
    return json_decode(file_get_contents($path), true);
}

function parseYaml(string $path): array
{
    return (array) Yaml::parseFile($path, Yaml::PARSE_OBJECT_FOR_MAP);
}