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

    public function __construct(User $user, OnlineStore $store, Appointment $appointment, array $services, $totalAmount, $isReschedule = false)
    {
        $this->user = $user;
        $this->store = $store;
        $this->appointment = $appointment;
        $this->services = $services;
        $this->totalAmount = $totalAmount;
        $this->isReschedule = $isReschedule;
    }

    public function build()
    {
        $subject = $this->isReschedule ? 'Appointment Rescheduling Confirmation' : 'Appointment Confirmation';
        return $this->subject($subject)
                    ->view('emails.appointment_confirmation')
                    ->with([
                        'userName' => $this->user->name,
                        'storeName' => $this->store->name ?? 'Online Store',
                        'appointmentDate' => $this->appointment->date,
                        'appointmentTime' => $this->appointment->time,
                        'appointmentType' => ucfirst($this->appointment->appointment_type),
                        'bookingNotes' => $this->appointment->booking_notes,
                        'services' => $this->services,
                        'totalAmount' => number_format($this->totalAmount, 2),
                        'isReschedule' => $this->isReschedule,
                    ]);
    }
}