<?php

namespace Softelebyte\Stubs;

class FromStubConfiguration
{
    private string $stub;
    private string $folder;
    private string $fileName;
    private string|array $replacesFrom;
    private string|array $replacesTo;
    private bool $notify = true;

    public static function make(): self
    {
        return new self;
    }

    public function setStub(string $stub): static
    {
        $this->stub = $stub;

        return $this;
    }

    public function stub(): string
    {
        return $this->stub;
    }

    public function setFolder(string $folder): static
    {
        $this->folder = $folder;

        return $this;
    }

    public function folder(): string
    {
        return $this->folder;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function fileName(): string
    {
        return $this->fileName;
    }

    public function setReplaces(array $replaces): static
    {
        $this->replacesFrom = array_map(
            fn (string $item) => '{{ ' . $item . ' }}',
            array_keys($replaces)
        );

        $this->replacesTo = array_values($replaces);

        return $this;
    }

    public function replacesFrom(): string|array
    {
        return $this->replacesFrom;
    }

    public function replacesTo(): string|array
    {
        return $this->replacesTo;
    }

    public function notify(?bool $notify = true): static
    {
        $this->notify = $notify;
        return $this;
    }

    public function shouldNotify(): bool
    {
        return $this->notify;
    }
}
