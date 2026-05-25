<?php

namespace Softelebyte\Stubs;

use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Illuminate\Support\Str;

class FromStubGenerator
{
    const FILE_CREATED = 'Se ha creado el archivo <fg=yellow>%s</>';

    private FromStubConfiguration $configuration;
    private ?OutputStyle $output = null;

    public function setConfiguration(FromStubConfiguration $configuration): self
    {
        $this->configuration = $configuration;
        return $this;
    }

    public function generate(?bool $force = false): ?string
    {
        $filePath = $this->folderPath() . DIRECTORY_SEPARATOR . $this->normalizeName($this->configuration->fileName());

        if (!$force && file_exists($filePath) && !$this->askForceGenerate($filePath)) {
            return null;
        }

        file_put_contents($filePath, $this->getContent());

        if ($this->configuration->shouldNotify()) {
            $this->notifyCreated($filePath);
        }

        return $filePath;
    }

    public function className(string $name, ?string $suffix = ''): string
    {
        return Str::studly($name) . $suffix;
    }

    public function getNamespace(string $folder): string
    {
        $folder = str_replace(DIRECTORY_SEPARATOR, '\\', $folder);
        return app()->getNamespace() . $folder;
    }

    private function folderPath(): string
    {
        $folderPath = app_path($this->configuration->folder());

        if (!is_dir($folderPath)) {
            mkdir($folderPath, recursive: true);
        }

        return $folderPath;
    }

    private function normalizeName(string $name): string
    {
        if (!str_ends_with($name, '.php')) {
            $name .= '.php';
        }

        return $name;
    }

    private function getContent(): string
    {
        $stub = file_get_contents($this->configuration->stub());

        return str_replace(
            $this->configuration->replacesFrom(),
            $this->configuration->replacesTo(),
            $stub
        );
    }

    private function output(): OutputStyle
    {
        if (!$this->output) {
            $this->output = new OutputStyle(
                new StringInput(''),
                new StreamOutput(fopen('php://stdout', 'w'))
            );
        }

        return $this->output;
    }

    private function notifyCreated(string $filePath): void
    {
        $this->output()->writeln(sprintf(self::FILE_CREATED, $filePath));
    }

    private function askForceGenerate(): bool
    {
        return $this->output()->confirm('The file already exists. Do you want replace it?', true);
    }
}
