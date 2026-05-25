<?php

namespace Softelebyte\QueryFilters\Commands;

use Illuminate\Console\Command;
use Softelebyte\Stubs\FromStubConfiguration;
use Softelebyte\Stubs\FromStubGenerator;
use Illuminate\Support\Str;

class CreateFilterCommand extends Command
{
    protected $signature = 'filters:create {name}';
    protected $description = 'Create query filter';

    public function handle(FromStubGenerator $generator): void
    {
        $name = $this->argument('name');
        $stub = __DIR__.DIRECTORY_SEPARATOR.'stubs'.DIRECTORY_SEPARATOR.'filter.stub';
        $className = $generator->className($name, 'Filter');
        $folder = config('query-filters.folder');

        $config = FromStubConfiguration::make()
            ->setStub($stub)
            ->setFolder($folder)
            ->setFileName($className)
            ->setReplaces([
                'namespace' => $generator->getNamespace($folder),
                'name' => $className,
                'field' => $this->guessField($name),
            ]);

        $generator->setConfiguration($config)->generate();
    }

    private function guessField(string $name): string
    {
        $field = strtolower(Str::singular($name));

        return $field.'_id';
    }
}
