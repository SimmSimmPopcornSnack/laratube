<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Comment;
use Symfony\Component\CssSelector\Node\FunctionNode;

class CommentController extends Controller
{
    public function index(Video $video) {
        return $video->comments()->paginate(10);
    }

    public function showReplies(Comment $comment) {
        return $comment->replies()->paginate(10);
    }

    public function store(Request $request, Video $video) {
        return auth()->user()->comments()->create([
            "body" => $request->body,
            "video_id" => $video->id,
            "parent_comment_id" => $request->parent_comment_id,
        ])->fresh();
    }
}
