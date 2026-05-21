<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    // Allows Laravel to safely map and write array payloads to these database columns
    protected $fillable = [
        'title',
        'content',
        'amharic_content',
        'category'
    ];
}