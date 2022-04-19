<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BoxMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $boxes;

    public function __construct(object $boxes_notified)
    {
        $this->boxes = $boxes_notified;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('alarmmanger@gmail.com', 'AM')->markdown('mail.box')->with(['boxes' => $this->boxes]);
    }
}
