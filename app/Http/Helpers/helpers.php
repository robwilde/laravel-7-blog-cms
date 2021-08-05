<?php

use App\Post;

function getPages()
{
    return Post::where('post_type', 'page')->where('is_published', '1')->get();
}