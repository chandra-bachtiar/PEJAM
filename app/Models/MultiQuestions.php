<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MultiQuestions extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'materi_id',
        'pertanyaan',
        'bobot',
    ];

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }

    public function multi_answers()
    {
        return $this->hasMany(MultiAnswers::class);
    }
}
