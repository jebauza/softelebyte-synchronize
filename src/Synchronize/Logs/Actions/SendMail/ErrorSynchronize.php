<?php

namespace Softelebyte\Synchronize\Logs\Actions\SendMail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorSynchronize extends Mailable
{
    use Queueable, SerializesModels;

    public string $errorException;

    public function __construct(string $errorException)
    {
        $this->errorException = $errorException;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.synchronize.error');
    }
}
