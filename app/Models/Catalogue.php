<?php

namespace App\Models;

use App\Models\Scopes\OwnedByUserScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy([OwnedByUserScope::class])]
class Catalogue extends Model
{
    /** @use HasFactory<\Database\Factories\CatalogueFactory> */
    use HasFactory;
    protected $fillable = ['user_id', 'name'];
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
