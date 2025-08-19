<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\OnlineStore;
use App\Models\Appointment;

class AppointmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $store;
    public $appointment;
    public $services;
    public $totalAmount;
    public $isReschedule;
    public $discount;

    public function __construct(User $user, OnlineStore $store, Appointment $appointment, array $services, $totalAmount, $isReschedule = false, $discount = null)
    {
        $this->user = $user;
        $this->store = $store;
        $this->appointment = $appointment;
        $this->services = $services;
        $this->totalAmount = $totalAmount;
        $this->isReschedule = $isReschedule;
        $this->discount = $discount;
    }

    public function build()
    {
        $subject = $this->isReschedule ? 'Appointment Rescheduling Confirmation' : 'Appointment Confirmation';
        return $this->subject($subject)
                    ->view('emails.appointment_confirmation')
                    ->with([
                        'user' => $this->user,
                        'store' => $this->store,
                        'appointment' => $this->appointment,
                        'services' => $this->services,
                        'totalAmount' => $this->totalAmount,
                        'isReschedule' => $this->isReschedule,
                        'discount' => $this->discount,
                    ]);
    }
}