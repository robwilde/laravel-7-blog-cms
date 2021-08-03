<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperGallery
 */
class Gallery extends Model
{
    protected  $fillable = ['user_id', 'image_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
