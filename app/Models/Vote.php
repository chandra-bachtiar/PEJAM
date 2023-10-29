<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function cadidate()
    {
        return $this->belongsTo(Cadidate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
