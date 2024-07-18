<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $path): mixed
{
    if (!file_exists($path)) {
        throw new \Exception("Invalid file path: {$path}");
    }

    $content = file_get_contents($path);
    $extension = pathinfo($path, PATHINFO_EXTENSION);

    if ($content === false) {
        throw new \Exception("Can't read file: {$path}");
    }
    switch ($extension) {
        case "json":
            return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        case "yml" || "yaml":
            return Yaml::parse($content);
        default:
            throw new \Exception("Format {$extension} not supported.");
    }
}
