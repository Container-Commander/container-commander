<?php

namespace App\Shell;

use App\Exceptions\DockerContainerMissingException;
use App\Shell\Environment;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Docker
{
    protected $shell;
    protected $formatter;
    protected $networking;
    protected $environment;

    public function __construct() {
        $this->shell = new Shell;
        $this->formatter = new DockerFormatter;
        $this->networking = new DockerNetworking;
        $this->environment = new Environment;
    }

    public function removeContainer(string $containerId): bool
    {
        if ($this->stoppableTakeoutContainers()->contains(function ($container) use ($containerId) {
            return $container['container_id'] === $containerId;
        })) {
            $this->stopContainer($containerId);
        }

        $process = $this->shell->exec('docker rm ' . $containerId);

        return $process->isSuccessful();
    }

    public function stopContainer(string $containerId): bool
    {
        if (! $this->stoppableTakeoutContainers()->contains(function ($container) use ($containerId) {
            return $container['container_id'] === $containerId;
        })) {
            throw new DockerContainerMissingException($containerId);
        }

        $process = $this->shell->exec('docker stop ' . $containerId);

        return $process->isSuccessful();
    }

    public function logContainer(string $containerId): void
    {
        if (! $this->stoppableTakeoutContainers()->contains(function ($container) use ($containerId) {
            return $container['container_id'] === $containerId;
        })) {
            throw new DockerContainerMissingException($containerId);
        }

        $process = $this->shell->exec('docker logs -f ' . $containerId);

        if (! $process->isSuccessful()) {
            throw new Exception('Failed to log container ' . $containerId);
        }
    }

    public function startContainer(string $containerId): bool
    {
        if (! $this->startableTakeoutContainers()->contains(function ($container) use ($containerId) {
            return $container['container_id'] === $containerId;
        })) {
            throw new DockerContainerMissingException($containerId);
        }

        $process = $this->shell->exec('docker start ' . $containerId);

        return $process->isSuccessful();
    }

    public function isInstalled(): bool
    {
        return $this->shell->execQuietly('docker --version 2>&1')->isSuccessful();
    }

    public function allContainers(): Collection
    {
        return $this->runAndParseTable(
            'docker ps --format "table {{.ID}}|{{.Names}}|{{.Status}}|{{.Ports}}"'
        );
    }

    public function takeoutContainers(): Collection
    {
        $process = sprintf(
            'docker ps -a --filter "name=TO-" --format "table %s|%s"',
            '{{.ID}}|{{.Names}}|{{.Status}}|{{.Ports}}',
            '{{.Label \"com.ejaaz.commander.Base_Alias\"}}|{{.Label \"com.ejaaz.commander.Full_Alias\"}}'
        );

        return $this->runAndParseTable($process);
    }

    public function startableTakeoutContainers(): Collection
    {
        return $this->takeoutContainers()->reject(function ($container) {
            return Str::contains($container['status'], 'Up');
        });
    }

    public function stoppableTakeoutContainers(): Collection
    {
        return $this->takeoutContainers()->filter(function ($container) {
            return Str::contains($container['status'], 'Up');
        });
    }

    public function volumeIsAvailable(string $volumeName): bool
    {
        return $this->listMatchingVolumes($volumeName)->isEmpty();
    }

    public function listMatchingVolumes(string $volumeName): Collection
    {
        return $this->runAndParseTable(
            "docker ps -a --filter volume={$volumeName} --format 'table {{.ID}}|{{.Names}}|{{.Status}}|{{.Ports}}'"
        );
    }

    public function imageIsDownloaded(string $organization, string $imageName, ?string $tag): bool
    {
        return $this->shell->execQuietly(sprintf(
            'docker image inspect %s/%s:%s',
            $organization,
            $imageName,
            $tag
        ))->isSuccessful();
    }

    public function downloadImage(string $organization, string $imageName, ?string $tag): void
    {
        $this->shell->exec(sprintf(
            'docker pull %s/%s:%s',
            $organization,
            $imageName,
            $tag
        ));
    }

    public function bootContainer(string $dockerRunTemplate, array $parameters): bool
    {
        $this->networking->ensureNetworkCreated();

        $command = sprintf(
            'docker run -d --name "${:container_name}" %s %s',
            $this->networking->networkSettings($parameters['alias'], $parameters['image_name']),
            $dockerRunTemplate
        );

        $process = $this->shell->exec($command, $parameters);

        return $process->isSuccessful();
    }

    public function attachedVolumeName(string $containerId)
    {
        $response = $this->shell->execQuietly("docker inspect --format='{{json .Mounts}}' {$containerId}");

        return optional(json_decode($response->getOutput()))[0]->Name ?? null;
    }

    public function isDockerServiceRunning(): bool
    {
        return $this->shell->execQuietly('docker info')->isSuccessful();
    }

    public function stopDockerService(): void
    {
        if ($this->environment->isWindowsOs()) {
            $this->shell->execQuietly('wsl -t docker-desktop');
            $this->shell->execQuietly('wsl -t docker-desktop-data');
        } elseif ($this->environment->isMacOs()) {
            $this->shell->execQuietly("test -z $(docker ps -q 2>/dev/null) && osascript -e 'quit app \"Docker\"'");
        } elseif ($this->environment->isLinuxOs()) {
            $this->shell->execQuietly('systemctl stop docker');
        } else {
            // BSD, Solaris, Unknown
            throw new Exception('Cannot stop Docker in PHP_OS_FAMILY ' . PHP_OS_FAMILY);
        }
    }

    protected function runAndParseTable(string $command): Collection
    {
        return $this->formatter->rawTableOutputToCollection(
            $this->shell->execQuietly($command)->getOutput()
        );
    }
}
