<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Post;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Tag;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.post.index')->with('posts', Post::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();

        if($categories->count() == 0 || $tags->count() == 0){

            Session::flash('info', 'You must have some categories and tags before attempting to create a post.');

            return redirect()->back();
        }

        return view('admin.post.create')->with('categories', $categories)
                                        ->with('tags', $tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // dd($request->all());

        $this->validate($request,[
            'title' => 'required|max:255',
            'featured' => 'required|image',
            'content' => 'required',
            'category' => 'required',
            'tags' => 'required'

        ]);

        $featured = $request->featured;
        $featured_new_name = time(). $featured->getClientOriginalName();
        $featured->move('uploads/posts', $featured_new_name);

        $post = Post::create([

            'title' => $request->title,
            'content' => $request->content,
            'featured' => 'uploads/posts/'. $featured_new_name,
            'category_id' => $request->category,
            'slug' =>str::slug($request->title)

        ]);

        $post->tags()->attach($request->tags);

        Session::flash('success', 'Post Created Successfully');


        // dd($request->all());

        return redirect()->route('post.list');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);

        return view('admin.post.edit')->with('post', $post)->with('categories', Category::all())
                                      ->with('tags', Tag::all());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        dd($request->all());
        $this->validate($request,[
            'title' => 'required',
            'content' => 'required',
            'category' => 'required'
        ]);

        $post = Post::find($id);

        if($request->hasFile('featured')){

            $featured = $request->featured;
            $featured_new_name = time(). $featured->getClientOriginalName();
            $featured->move('uploads/posts', $featured_new_name);
            $post->featured = 'uploads/posts/' .$featured_new_name;
        }

        $post->title = $request->title;
        $post->content = $request->content;
        $post->category_id = $request->category;

        $post->save();

        $post->tags()->sync($request->tags);

        Session::flash('success', 'Post updated successfully.');

        return redirect(route('post.list'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        $post->delete();

        Session::flash('success', 'Your post was just trashed.');

        return redirect()->back();

    }

    public function trashed(){

        $posts = Post::onlyTrashed()->get();
        // dd($posts);
        return view('admin.post.trashed')->with('posts', $posts);
    }

    public function kill($id){

        $post = Post::withTrashed()->where('id', $id)->first();

        $post->forceDelete();

        Session::flash('success', 'Post Deleted Permanently.');

        return redirect()->back();
    }

    public function restore($id){

        $post = Post::withTrashed()->where('id', $id)->first();

        $post->restore();

        Session::flash('success', 'Post Restored Successfully!');

        return redirect()->route('post.list');
    }

}
