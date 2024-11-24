<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checklist extends Model
{
    use HasFactory;

    protected $table = 'checklists';

    protected $fillable = [
        'name',
        'description',
        'person_id',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ChecklistItem::class, 'checklist_id')->orderBy('order');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
