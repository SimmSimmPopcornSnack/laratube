<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Channel;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $query = request()->search;

        $videos = collect();
        $channels = collect();

        if($query) {
            $videos = Video::where("title", "LIKE", "%{$query}%")->where("description", "LIKE", "%{$query}%")->paginate(5, ["*"], "video_page");
            $channels = Channel::where("name", "LIKE", "%{$query}%")->where("description", "LIKE", "%{$query}%")->paginate(5, ["*"], "channel_page");
        }

        return view('home')->with([
            "videos" => $videos,
            "channels" => $channels,
        ]);
    }
}
