<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post\Post;
use App\Models\Post\PostChannel;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Post::join(
            'post_channels',
            'posts.post_channel_id',
            '=',
            'post_channels.id'
        )
            ->join('users', 'users.id', '=', 'post_channels.user_id')
            ->where('post_channels.user_id', Auth::id())
            ->select(
                'post_channels.*',
                'posts.*',
                'users.id as author_id',
                'users.name as author_name'
            )
            ->orderBy('posts.created_at', 'desc')
            ->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (
            !count(
                PostChannel::where('user_id', (int) Auth::id())
                    ->where('id', (int) $request['post_channel_id'])
                    ->get()
            )
        ) {
            return json_encode([
                'status' => 401,
                'message' => "Such channel doesn't exist for given user.",
            ]);
        }

        $request->validate([
            'post_title' => 'required',
            'post_channel_id' => 'required',
            'post_short_description' => 'required',
            'post_content' => 'required',
            'post_publication_date' => 'required',
            'post_tags' => 'required',
        ]);

        return Post::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Post::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        // get authenticated user id
        $user_id = Auth::id();

        // get post channel id
        $post_channel_id = Post::where('id', $id)->value('post_channel_id');

        // check if user owns the post
        $user_owns_post = PostChannel::where([
            ['user_id', '=', $user_id],
            ['id', '=', $post_channel_id],
        ])->exists();

        // if user owns post update post data
        if ($user_owns_post) {
            Post::where('id', $id)->update([
                'post_title' => $request['post_title'],
                'post_short_description' => $request['post_short_description'],
                'post_content' => $request['post_content'],
                'post_publication_date' => $request['post_publication_date'],
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Post::where('id', $id)->delete();
    }
}
