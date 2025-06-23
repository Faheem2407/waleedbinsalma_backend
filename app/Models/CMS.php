<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CMS extends Model
{
    use HasFactory;

    protected $table = 'c_m_s';

    protected $fillable = [
        'page',
        'section',
        'title',
        'sub_title',
        'description',
        'short_description',
        'satisfied_clients',
        'pro_consultants',
        'years_in_businesses',
        'successful_cases',
        'file_url',
        'background_image',
        'icon',
        'button_text',
        'button_link',
        'status',
    ];
}
