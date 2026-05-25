<?php

namespace Softelebyte\Synchronize\Base\Command;

use Exception;
use Illuminate\Console\Command;
use Softelebyte\Synchronize\Base\Services\ConfigService;

class PrintConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synchronize:print_config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imprime como un json los distintos tipos de groupo de sincronizaciones de los que disponemos';

    private ConfigService $configService;

    public function __construct(ConfigService $configService)
    {
        parent::__construct();
        $this->configService = $configService;
    }

    /**
     * @throws Exception
     */
    public function handle()
    {

        $config = $this->configService->groupTypes();
        echo json_encode($config);
    }
}