<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentTeamAssignment extends Model
{
    protected $table = 'appointment_team_assignments';

    protected $fillable = [
        'appointment_id',
        'team_id',
        'team_member_status',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}