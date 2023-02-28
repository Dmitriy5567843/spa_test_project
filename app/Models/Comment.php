<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'content',
        'parent_id',
    ];
    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

}
