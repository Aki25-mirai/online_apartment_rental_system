<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; 
use App\Township;
use App\Type;
use App\Post;
use App\User;
use DB;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct($value='')
    {
        $this->middleware('auth')->only('create');
    }

    // can view ad posts by any user without login
    public function index()
    {
        $posts=DB::table('posts')
            ->where('status', '=', '0')
            ->get();

        return view('frontend.posts.index',compact('posts'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $townships = Township::all();
        $types = Type::all();
        $user_id =Auth::id();
        return view('frontend.userposts.create',compact('townships','types'));
        
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'title' => 'required',
            'photo' => 'required',
            'monthly_fees' => 'required',
            'length' => 'required',
            'width' => 'required',
            'bedrooms' => 'required',
            'bathrooms' => 'required',
            'description' => 'required',
            'status'    => 'required',

        ]);

        // File Upload
        $imageName = time().'.'.$request->photo->extension();

        $request->photo->move(public_path('frontendtemplate/post_img'),$imageName);
        $myfile = 'frontendtemplate/post_img/'.$imageName;

        // Store Data
        $post = new Post;
        $post->title = $request->title;
        $post->photo = $myfile;
        $post->monthly_fees = $request->monthly_fees;
        $post->length = $request->length;
        $post->width = $request->width;
        $post->bedrooms = $request->bedrooms;
        $post->bathrooms = $request->bathrooms;
        $post->description = $request->description;
        $post->type_id = $request->type;
        $post->township_id = $request->township;
        $post->user_id =Auth::id();
        $post->status = $request->status;
        // 'role_id' => $data['role_id'],

        $post->save();

        // Redirect
        // Alert::success('Success!', 'New Item Inserted Successfully.');

        return redirect()->route('posts.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //can view post detail by any user without login
    public function show($id)
    {
         $post = Post::findOrFail($id);
        return view('frontend.posts.detail',compact('post'));

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
        $types = Type::all();
        $townships = Township::all();
        return view('frontend.userposts.edit',
            compact('post','types','townships'));
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
        

        $request->validate([
            'title' => 'required',
            // 'photo' => 'required',=> may be present or absent
            'monthly_fees' => 'required',
            'length' => 'required',
            'width' => 'required',
            'bedrooms' => 'required',
            'bathrooms' => 'required',
            'description' => 'required',
            'type'=>'required',
            'township'=>'required',
            'status' =>'required',
            
        ]);

        

        // File Upload
        if ($request->hasFile('photo')) {
            $imageName = time().'.'.$request->photo->extension();

            $request->photo->move(public_path('frontendtemplate/post_img'),$imageName);
            $myfile = 'frontendtemplate/post_img/'.$imageName;
        }

        // if upload new image, delete old image;
        
        // Update Data
        $post = Post::find($id);
        $post->title = $request->title;
        if ($request->hasFile('photo')) {
                File::delete($post->photo);
                $post->photo = $myfile;  
            }else{
                $post->photo = $post->photo;
            }
        
        $post->monthly_fees = $request->monthly_fees;
        $post->length = $request->length;
        $post->width = $request->width;
        $post->bedrooms = $request->bedrooms;
        $post->bathrooms = $request->bathrooms;
        $post->description = $request->description;
        $post->type_id = $request->type;
        $post->township_id = $request->township;
        $post->status = $request->status;
        // $post->user_id =Auth::id();

        $post->save();

        // Redirect
        // Alert::success('Success!', ' Your post is updated Successfully.');

        return redirect()->route('posts.index');
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


        // delete related file from storage
        // File::delete($post->photo);


        $post->delete();

        // Alert::success('Success!', 'Post('.$post->id.') Deleted Successfully.');
        
        return redirect()->route('posts.index');
    }
}
