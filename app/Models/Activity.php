<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Person;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'person_id',
        'start_time',
        'end_time',
        'day_of_week'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
