<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $categories = Category::orderBy('id', 'DESC')->get();

        return view('admin.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.category.create');
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
        $this->validate($request,
            [
                'thumbnail' => 'required',
                'name' => 'required|unique:categories'
            ],
            [
                'thumbnail.required' => 'Enter thumbnail url',
                'name.required' => 'Enter name',
                'name.unique' => 'Category already exist',
            ]);

        $category = new Category();
        $category->thumbnail = $request->get('thumbnail');
        $category->user_id = Auth::id();
        $category->name = $request->get('name');
        $category->slug = str_slug($request->get('name'));
        $category->is_published = $request->get('is_published');
        $category->save();

        Session::flash('message', 'Category created successfully');

        return redirect()->route('categories.index');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->validate($request,
            [
                'thumbnail' => 'required',
                'name' => 'required|unique:categories,name,' . $category->id,
            ],
            [
                'thumbnail.required' => 'Enter thumbnail url',
                'name.required' => 'Enter name',
                'name.unique' => 'Category already exist',
            ]);

        $category->thumbnail = $request->get('thumbnail');
        $category->user_id = Auth::id();
        $category->name = $request->get('name');
        $category->slug = str_slug($request->get('name'));
        $category->is_published = $request->get('is_published');
        $category->save();

        Session::flash('message', 'Category updated successfully');

        return redirect()->route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        Session::flash('delete-message', 'Category deleted successfully');

        return redirect()->route('categories.index');
    }
}
