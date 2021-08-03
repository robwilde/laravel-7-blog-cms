<?php

namespace App\Http\Controllers;

use App\Gallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $galleries = Gallery::orderBy('id', 'DESC')->get();

        return view('admin.gallery.index', compact('galleries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.gallery.create');
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
            "image_url" => 'required',
        ],
            [
                'image_url.required' => 'Select image',
            ]
        );

        foreach ($request->file('image_url') as $image_url) {
            // Get file name with extension
            $fileNameWithExt = $image_url->getClientOriginalName();

            // Get just file name
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

            // Get just file extension
            $fileExt = $image_url->getClientOriginalExtension();

            // Get file name to store
            $fileNameToStore = $fileName . '_' . time() . '.' . $fileExt;

            $gallery = new Gallery();
            $gallery->user_id = Auth::id();
            $gallery->image_url = $fileNameToStore;
            $save = $gallery->save();

            if ($save) {
                $image_url->storeAs('public/galleries', $fileNameToStore);
            }
        }

        Session::flash('message', 'Images uploaded successfully');

        return redirect()->route('galleries.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Gallery  $gallery
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Gallery $gallery): RedirectResponse
    {
        // Delete image file
        Storage::delete('/public/galleries/' . $gallery->image_url);

        // Delete data from table
        $gallery->delete();

        Session::flash('delete-message', 'Image deleted successfully');

        return redirect()->route('galleries.index');
    }
}
