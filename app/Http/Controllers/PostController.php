<?php

namespace App\Http\Controllers;

use App\Category;
use App\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $posts = Post::orderBy('id', 'DESC')
            ->where('post_type', 'post')
            ->get();

        return view('admin.post.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::orderBy('name', 'ASC')->pluck('name', 'id');

        return view('admin.post.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            "thumbnail" => 'required',
            "title" => 'required|unique:posts',
            "details" => "required",
            "category_id" => "required"
        ],
            [
                'thumbnail.required' => 'Enter thumbnail url',
                'title.required' => 'Enter title',
                'title.unique' => 'Title already exist',
                'details.required' => 'Enter details',
                'category_id.required' => 'Select categories',
            ]
        );

        $post = new  Post();
        $post->user_id = Auth::id();
        $post->thumbnail = $request->get('thumbnail');
        $post->title = $request->get('title');
        $post->slug = str_slug($request->get('title'));
        $post->sub_title = $request->get('sub_title');
        $post->details = $request->get('details');
        $post->is_published = $request->get('is_published');
        $post->post_type = 'post';
        $post->save();

        $post->categories()->sync($request->get('category_id'), false);

        Session::flash('message', 'Post created successfully');

        return redirect()->route('posts.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Post $post)
    {
        $categories = Category::orderBy('name', 'ASC')->pluck('name', 'id');

        return view('admin.post.edit', compact('categories', 'post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->validate($request, [
            "thumbnail" => 'required',
            'title' => 'required|unique:posts,title,' . $post->id . ',id', // ignore this id
            'details' => 'required',
            "category_id" => "required"
        ],
            [
                'thumbnail.required' => 'Enter thumbnail url',
                'title.required' => 'Enter title',
                'title.unique' => 'Title already exist',
                'details.required' => 'Enter details',
                'category_id.required' => 'Select categories',
            ]
        );

        $post->user_id = Auth::id();
        $post->thumbnail = $request->get('thumbnail');
        $post->title = $request->get('title');
        $post->slug = str_slug($request->get('title'));
        $post->sub_title = $request->get('sub_title');
        $post->details = $request->get('details');
        $post->is_published = $request->get('is_published');
        $post->save();

        $post->categories()->sync($request->get('category_id'));

        Session::flash('message', 'Post updated successfully');

        return redirect()->route('posts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        Session::flash('delete-message', 'Post deleted successfully');

        return redirect()->route('posts.index');
    }
}
