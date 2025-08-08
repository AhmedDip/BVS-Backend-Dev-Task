<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['title','content','user_id','status','published_at'];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
