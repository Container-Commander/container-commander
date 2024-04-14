<?php

namespace App\Shell;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DockerFormatter
{
    /**
     * Given the raw string of output from Docker, return a collection of
     * associative arrays, with the keys lowercased and slugged using underscores.
     *
     * @param string $output Docker command output
     * @return Collection     Collection of associative arrays
     */
    public function rawTableOutputToCollection($output): Collection
    {
        $containers = collect(explode("\n", trim($output)))->map(function ($line) {
            return explode('|', $line);
        })->filter();

        $keys = array_map('App\Shell\underscore_slug', $containers->shift());

        if ($containers->isEmpty()) {
            return $containers;
        }

        return $containers->map(function ($container) use ($keys) {
            return array_combine($keys, $container);
        });
    }
}
function underscore_slug(string $string): string
{
    return Str::slug($string, '_');
}
