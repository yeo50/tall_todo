<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskBrief extends Model
{
    /** @use HasFactory<\Database\Factories\TaskBriefFactory> */
    use HasFactory;
    protected $fillable = ['task_id', 'outline', 'note'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
