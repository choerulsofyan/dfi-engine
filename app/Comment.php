<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['article_id', 'content', 'email', 'name'];

    /* public function user()
    {
        return $this->belongsTo(User::class);
    } */

    public function articles()
    {
        return $this->belongsTo(Article::class);
    }
}
