<?php

namespace App\Models;

use App\Models\Scopes\OwnedByUserScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([OwnedByUserScope::class])]
class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'user_id',
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
