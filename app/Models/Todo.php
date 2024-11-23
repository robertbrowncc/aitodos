<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Person;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'completed',
        'url',
        'person_id'
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
