<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EquipMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $equips;

    public function __construct(object $equips_notified)
    {
        $this->equips = $equips_notified;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('alarmmanger@gmail.com', 'AM')->markdown('mail.equip')->with(['equips' => $this->equips]);
    }
}
