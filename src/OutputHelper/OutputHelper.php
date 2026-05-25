<?php

namespace Softelebyte\OutputHelper;


use Carbon\Carbon;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Softelebyte\OutputHelper\Fields\OutputHelperFields;

trait OutputHelper
{
    /**
     * @var OutputStyle
     */
    protected $output;
    /**
     * @var ProgressBar
     */
    protected $bar;
    /**
     * @var Carbon
     */
    protected $startCommand;

    protected function initializeOutput($total): ?ProgressBar
    {
        $this->output = resolve(OutputHelperFields::CONSOLE_OUTPUT);
        $this->startCommand = Carbon::now();
        $this->text('Start: ' . $this->startCommand->format('Y-m-d h:i:s'));
        if (is_array($total)) {
            $total = count($total);
        }
        $this->bar = $this->output->createProgressBar($total);
        $this->bar->start();
        return $this->bar;
    }

    public function finishBar(): void
    {
        if(!$this->bar){
            return;
        }
        $finish = Carbon::now();
        $this->bar->finish();
        $this->text('Finish: ' . $finish->format('Y-m-d h:i:s') . ' Tiempo en segundos: ' . $finish->diffInSeconds($this->startCommand, true));
    }
    protected function advanceBar(): void
    {
        if(!$this->bar){
            return;
        }
        $this->bar->advance();
    }

    protected function text($text): void
    {
        if(!$this->output){
            return;
        }
        $this->output->text($text);
    }

    protected function write($text): void
    {
        if(!$this->output){
            return;
        }
        $this->output->write($text);
    }
}
