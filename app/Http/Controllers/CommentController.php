<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;
use DB;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comment = Comment::orderBy('created_at', 'DESC')->get();
        return response()->json($comment);
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
            'article_id' => 'required|integer',
            'email' => 'required|string',
            'name' => 'required|string',
            'content' => 'required|string'
        ]);
        
        $comment = Comment::create([
            'article_id' => $request->article_id,
            'email' => $request->email,
            'name' => $request->name,
            'content' => $request->content
        ]);

        return response()->json($comment, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        return response()->json($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        $this->validate($request, [
            'content' => 'required|string'
        ]);

        $comment->update($request->all());

        return response()->json($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return response()->json('Category deleted successfully');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function recent($offset = 0, $limit = 12)
    {
        $comments = DB::table('comments')
            ->join('articles', 'comments.article_id', '=', 'articles.id')
            ->select('comments.content', 'articles.title as article', 'comments.created_at')
            ->offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'DESC')->get();

        return response()->json($comments);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function countComments()
    {
        $comments_count = DB::table('comments')->count();
        $data = array("status" => 200, "results" => $comments_count);

        return response()->json($data);
    }
}
