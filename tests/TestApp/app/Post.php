<?php

namespace TestApp;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string headline_slug The URL slug for the post's headline
 */
class Post extends Model
{
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function getHeadlineSlugAttribute(): string
    {
        return '';
    }
}