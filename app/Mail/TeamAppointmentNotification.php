<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\Team;
use App\Models\OnlineStore;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamAppointmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $team;
    public $store;
    public $services;

    public function __construct(Appointment $appointment, Team $team, OnlineStore $store, array $services)
    {
        $this->appointment = $appointment;
        $this->team = $team;
        $this->store = $store;
        $this->services = $services;
    }

    public function build()
    {
        return $this->subject('New Appointment Assignment')
                    ->view('emails.team_appointment_notification')
                    ->with([
                        'appointment' => $this->appointment,
                        'team' => $this->team,
                        'store' => $this->store,
                        'services' => $this->services,
                        'accept_url' => route('appointment.team.accept', ['appointment_id' => $this->appointment->id, 'team_id' => $this->team->id]),
                        'decline_url' => route('appointment.team.decline', ['appointment_id' => $this->appointment->id, 'team_id' => $this->team->id]),
                    ]);
    }
}