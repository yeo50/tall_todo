<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskBrief extends Model
{
    protected $fillable = ['task_id', 'outline', 'note'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
