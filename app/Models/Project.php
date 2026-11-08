<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = ['name'];

    protected function casts(): array
    {
        return ['order_version' => 'integer'];
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->orderBy('priority');
    }
}
