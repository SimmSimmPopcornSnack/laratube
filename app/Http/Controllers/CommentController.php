<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;

class CommentController extends Controller
{
    public function index(Video $video) {
        return $video->commnets()->paginate(5);
    }
}
