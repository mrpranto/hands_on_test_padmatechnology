<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'name', 'is_favorite'
    ];

    public function contactDetails(): HasMany
    {
        return $this->hasMany(ContactDetails::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    public function contactImages(): HasMany
    {
        return $this->hasMany(ContactImage::class);
    }
}
