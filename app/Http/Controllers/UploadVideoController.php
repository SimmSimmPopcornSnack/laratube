<?php

namespace App\Http\Controllers;

use App\Jobs\Videos\ConvertForStreaming;
use App\Jobs\Videos\CreateVideoThumbnail;
use Illuminate\Http\Request;
use App\Models\Channel;

class UploadVideoController extends Controller
{
    public function index(Channel $channel) {
        return view("channels.upload", [
            "channel" => $channel,
        ]);
    }

    public function store(Channel $channel) {
        $video = $channel->videos()->create([
            "title" => request()->title,
            "path" => request()->video->store("channels/{$channel->id}"),
        ]);
        $this->dispatch(new CreateVideoThumbnail($video));
        $this->dispatch(new ConvertForStreaming($video));
        return $video;
    }
}
