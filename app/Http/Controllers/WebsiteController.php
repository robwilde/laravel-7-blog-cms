<?php

namespace App\Http\Controllers;

use App\Mail\VisitorContact;
use App\Post;
use App\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class WebsiteController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $categories = Category::orderBy('name', 'ASC')
            ->where('is_published', '1')
            ->get();

        $posts = Post::orderBy('publish_date', 'DESC')
            ->where('post_type', 'post')
            ->where('is_published', '1')
            ->paginate(5);

        return view('website.index', compact('posts', 'categories'));
    }

    /**
     * @param  string  $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function post(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->where('post_type', 'post')
            ->where('is_published', '1')
            ->first();

        if (!$post) {
            return \Response::view('website.errors.404', [], 404);
        }

        if (!$post['thumbnail']) {
            $categories = $post->categories()->pluck('slug')->toArray();

            if (empty($categories)) {
                $post->thumbnail = 'https://source.unsplash.com/collection/8807226/1920x1080';
            } else {
                // https://source.unsplash.com/
                $searchTerms = count($categories) > 1 ? implode(',', $categories) : $categories[0];
                $post->thumbnail = "https://source.unsplash.com/1920x1080/?{$searchTerms}";
            }
        }

        return view('website.post', compact('post'));
    }

    /**
     * @param  string  $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->where('is_published', '1')->first();

        if (!$category) {
            return \Response::view('website.errors.404', [], 404);
        }

        $posts = $category->posts()->orderBy('posts.id', 'DESC')->where('is_published', '1')->paginate(5);

        return view('website.category', compact('category', 'posts'));
    }

    /**
     * @param  string  $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function page(string $slug)
    {
        $page = Post::where('slug', $slug)
            ->where('post_type', 'page')
            ->where('is_published', '1')
            ->first();

        if (!$page) {
            return \Response::view('website.errors.404', [], 404);
        }

        return view('website.page', compact('page'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showContactForm()
    {
        return view('website.contact');
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitContactForm(Request $request): RedirectResponse
    {
        $data = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'tel' => $request->get('tel'),
            'message' => $request->get('message'),
        ];

        Mail::to(env('EMAIL_ADDRESS'))->send(new VisitorContact($data));

        Session::flash('message', 'Thank you for your email');

        return redirect()->route('contact.show');
    }
}
