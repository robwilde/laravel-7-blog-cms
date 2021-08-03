<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $pages = Post::orderBy('id', 'DESC')->where('post_type', 'page')->get();

        return view('admin.page.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.page.create');
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
        ],
            [
                'thumbnail.required' => 'Enter thumbnail url',
                'title.required' => 'Enter title',
                'title.unique' => 'Title already exist',
                'details.required' => 'Enter details',
            ]
        );

        $page = new  Post();
        $page->user_id = Auth::id();
        $page->thumbnail = $request->get('thumbnail');
        $page->title = $request->get('title');
        $page->slug = str_slug($request->get('title'));
        $page->sub_title = $request->get('sub_title');
        $page->details = $request->get('details');
        $page->is_published = $request->get('is_published');
        $page->post_type = 'page';
        $page->save();

        Session::flash('message', 'Page created successfully');

        return redirect()->route('pages.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(int $id)
    {
        $page = Post::findOrFail($id);

        return view('admin/page/edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $this->validate($request, [
            "thumbnail" => 'required',
            'title' => 'required|unique:posts,title,' . $id . ',id',
            'details' => 'required',
        ],
            [
                'thumbnail.required' => 'Enter thumbnail url',
                'title.required' => 'Enter title',
                'title.unique' => 'Title already exist',
                'details.required' => 'Enter details',
            ]
        );

        $page = Post::findOrFail($id);
        $page->user_id = Auth::id();
        $page->thumbnail = $request->get('thumbnail');
        $page->title = $request->get('title');
        $page->slug = str_slug($request->get('title'));
        $page->sub_title = $request->get('sub_title');
        $page->details = $request->get('details');
        $page->is_published = $request->get('is_published');
        $page->save();

        Session::flash('message', 'Page updated successfully');

        return redirect()->route('pages.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(int $id): RedirectResponse
    {
        $page = Post::findOrFail($id);
        $page->delete();

        Session::flash('delete-message', 'Page deleted successfully');

        return redirect()->route('pages.index');
    }
}
