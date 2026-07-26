<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['content', 'type', 'title', 'level', 'target_audience', 'class_id'];
    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

     public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}
