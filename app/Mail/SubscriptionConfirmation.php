<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\OnlineStore;
use App\Models\Subscription;

class SubscriptionConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $store;
    public $subscription;
    public $amount;

    public function __construct(User $user, OnlineStore $store, Subscription $subscription, $amount)
    {
        $this->user = $user;
        $this->store = $store;
        $this->subscription = $subscription;
        $this->amount = $amount;
    }

    public function build()
    {
        return $this->subject('Subscription Confirmation')
                    ->view('emails.subscription_confirmation')
                    ->with([
                        'userName' => $this->user->name,
                        'storeName' => $this->store->name ?? 'Online Store',
                        'startDate' => $this->subscription->start_date->format('M d, Y'),
                        'endDate' => $this->subscription->end_date->format('M d, Y'),
                        'amount' => number_format($this->amount, 2),
                        'isRenewal' => $this->subscription->is_renew,
                    ]);
    }
}