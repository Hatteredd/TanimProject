<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'location',
        'specialty',
        'contact_number',
        'status',
        'notes',
    ];
}
