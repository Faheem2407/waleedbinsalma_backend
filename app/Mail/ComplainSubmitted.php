<?php

// app/Mail/ComplainSubmitted.php

namespace App\Mail;

use App\Models\Complain;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplainSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $complain;
    public $store;
    public $storeOwner;
    public $customer;

    public function __construct(Complain $complain)
    {
        $this->complain = $complain;
        $this->store = $complain->store;
        $this->storeOwner = $this->store->user ?? null;
        $this->customer = $complain->user;
    }

    public function build()
    {
        return $this->subject('🛑 New Store Complaint Received')
            ->markdown('emails.complain.submitted');
    }
}
