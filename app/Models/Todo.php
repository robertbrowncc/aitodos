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

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    protected $with = ['person'];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
