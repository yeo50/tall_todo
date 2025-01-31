<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    protected $fillable = [
        'catalogue_id',
        'name',
        'due',
        'remider',
    ];
    public function catalogue()
    {
        return $this->belongsTo(Catalogue::class);
    }
}
