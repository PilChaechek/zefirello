<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'creator_id',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the tasks for the project.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
