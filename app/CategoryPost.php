<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCategoryPost
 */
class CategoryPost extends Model
{
    protected $fillable = ['category_id', 'post_id'];
}
