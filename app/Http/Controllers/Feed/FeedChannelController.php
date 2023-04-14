<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post\Post;
use App\Models\Post\PostChannel;
use App\Models\Feed\UserSubscribedChannel;
use Illuminate\Support\Facades\Auth;

class FeedChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Post::join('post_channels', 'posts.post_channel_id', '=', 'post_channels.id')
        ->join('user_subscribed_channels', 'user_subscribed_channels.post_channel_id', '=', 'post_channels.id')
        ->join('users', 'users.id', '=', 'post_channels.user_id')
        ->where('user_subscribed_channels.user_id', Auth::id())
        ->where('posts.post_publication_date','<=', now())
        ->select('post_channels.*','user_subscribed_channels.*','posts.*', 'users.id as author_id', 'users.name as author_name')
        ->orderBy('posts.created_at','desc')
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
        $request->validate([
            'post_channel_id' => 'required',
        ]);

        // Prevent user from subscribing to his own channels
        $user_channels = PostChannel::where('user_id', (int) Auth::id())
            ->where('id', $request['post_channel_id'])
            ->get();

        if (count($user_channels)) {
            return 'You cannot subcribe to your own channels.';
        }

        $check = UserSubscribedChannel::where('user_id',Auth::id())->where('post_channel_id',$request['post_channel_id'])->get();
        if(count($check)){
            return "You've subscribed to this channel already";
        }

        // Check for foreign key exceptions (and other query exceptions too)
        try {
            return UserSubscribedChannel::create(
                array_merge($request->all(), ['user_id' => Auth::id()])
            );
        } catch (\Illuminate\Database\QueryException $e) {
            return 'Something went wrong. Please try again.';
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (
            !count(
                PostChannel::where('user_id', (int) Auth::id())
                    ->where('id', (int) $id)
                    ->get()
            )
        ) {
            return json_encode([
                'status' => 403,
                'message' => "You don't have access to view this channel",
            ]);
        }

        return Post::where('post_channel_id', $id)->get();
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUnsubscribedChannels()
    {
        return PostChannel::whereNotIn('post_channels.id', function ($query) {
            $query->select('post_channel_id')
                ->from(with(new UserSubscribedChannel)->getTable())
                ->where('user_id', Auth::id());
        })->where('user_id', '!=', Auth::id())
        ->join('users', 'users.id', '=', 'post_channels.user_id')
        ->select('post_channels.*', 'users.id as owner_id', 'users.name as username')
        ->get();
    }
}
