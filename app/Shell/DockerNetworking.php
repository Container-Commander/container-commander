<?php

namespace App\Shell;

use Illuminate\Support\Collection;

class DockerNetworking
{
    protected $shell;
    protected $formatter;

    public function __construct(Shell $shell, DockerFormatter $formatter)
    {
        $this->shell = $shell;
        $this->formatter = $formatter;
    }

    public function networkSettings(string $alias, string $image_name): string
    {
        $networkSettings = [
            '--network=commander',
            '--network-alias="${:alias}"',
            '--label com.ejaaz.commander.Full_Alias=' . $alias,
        ];

        if (! $this->baseAliasExists($image_name)) {
            $networkSettings[] = '--network-alias="' . $image_name . '"';
            $networkSettings[] = '--label=com.ejaaz.commander.Base_Alias=' . $image_name;
        }

        return implode(' ', $networkSettings);
    }

    public function ensureNetworkCreated($name = 'commander'): void
    {
        if ($this->listMatchingNetworks()->isEmpty()) {
            $this->shell->execQuietly('docker network create -d bridge ' . $name);
        }
    }

    public function baseAliasExists(string $name): bool
    {
        $output = $this->shell->execQuietly(
            'docker ps --filter "label=com.ejaaz.commander.Base_Alias=' . $name . '" --format \n
            "table {{.ID}}|{{.Names}}"'
        )->getOutput();

        $collection = $this->formatter->rawTableOutputToCollection($output);

        return $collection->isNotEmpty();
    }

    public function listMatchingNetworks(string $networkName = 'commander'): Collection
    {
        $command = "docker network ls --filter name={$networkName} --format 'table {{.ID}}|{{.Name}}'";
        $output = $this->shell->execQuietly($command)->getOutput();

        return $this->formatter->rawTableOutputToCollection($output);
    }
}
