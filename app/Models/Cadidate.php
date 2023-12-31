<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cadidate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ketua',
        'wakil',
        'image',
        'description',
    ];

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
