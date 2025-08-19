<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\OnlineStore;
use App\Models\Appointment;

class AppointmentCancellation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $store;
    public $appointment;

    public function __construct(User $user, OnlineStore $store, Appointment $appointment)
    {
        $this->user = $user;
        $this->store = $store;
        $this->appointment = $appointment;
    }

    public function build()
    {
        return $this->subject('Appointment Cancellation Confirmation')
                    ->view('emails.appointment_cancellation')
                    ->with([
                        'userName' => $this->user->name,
                        'storeName' => $this->store->name ?? 'Online Store',
                        'appointmentDate' => $this->appointment->date,
                        'appointmentTime' => $this->appointment->time,
                        'appointmentType' => ucfirst($this->appointment->appointment_type),
                        'bookingNotes' => $this->appointment->booking_notes,
                    ]);
    }
}