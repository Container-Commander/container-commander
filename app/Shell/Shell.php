<?php

namespace App\Shell;

use Symfony\Component\Process\Process;

class Shell
{

    public function exec(string $command, array $parameters = [], bool $quiet = false): Process
    {
        $process = $this->buildProcess($command);
        $process->run(function ($type, $buffer) use ($quiet) {
            if (empty($buffer) || $buffer === PHP_EOL || $quiet) {
                return;
            }
        }, $parameters);

        return $process;
    }

    public function buildProcess(string $command): Process
    {
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(null);

        return $process;
    }

    public function execQuietly(string $command, array $parameters = []): Process
    {
        return $this->exec($command, $parameters, $quiet = true);
    }

    public function formatMessage(string $buffer, $isError = false): string
    {
        $pre = $isError ? '<bg=red;fg=white> ERR </> %s' : '<bg=green;fg=white> OUT </> %s';

        return rtrim(collect(explode("\n", trim($buffer)))->reduce(function ($carry, $line) use ($pre) {
            return $carry .= trim(sprintf($pre, $line)) . "\n";
        }, ''));
    }

    public function formatErrorMessage(string $buffer)
    {
        return $this->formatMessage($buffer, true);
    }
}
