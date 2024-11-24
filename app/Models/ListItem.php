<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'order',
        'completed',
        'list_id',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'order' => 'integer',
    ];

    public function list(): BelongsTo
    {
        return $this->belongsTo(CustomList::class, 'list_id');
    }
}
