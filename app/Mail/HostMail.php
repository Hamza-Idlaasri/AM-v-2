<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HostMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $hosts;

    public function __construct(object $hosts_notified)
    {
        $this->hosts = $hosts_notified;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('alarmmanger@gmail.com', 'AM')->markdown('mail.host')->with(['hosts' => $this->hosts]);
    }
}
