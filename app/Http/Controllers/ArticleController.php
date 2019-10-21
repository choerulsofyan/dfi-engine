<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;
use DB;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = DB::table('articles')
        ->join('categories', 'articles.category_id', '=', 'categories.id')
        ->join('users', 'articles.user_id', '=', 'users.id')
        ->select('articles.id', 'articles.title', 'articles.content', 'articles.image', 'categories.name AS category', 'articles.created_at', 'users.name as author', 'articles.status')
            ->orderBy('created_at', 'DESC')->get();

        foreach ($articles as $key => $value) {
            $articles[$key]->created_at = date('d F Y', strtotime($value->created_at));
            $articles[$key]->title = str_limit($value->title, 16);
            $articles[$key]->content = str_limit($value->content, 100);
        }

        $data = array("status" => 200, "results" => $articles);

        return response()->json($data);
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
            'title' => 'required|string|unique:articles',
            'content' => 'required|string',
            'image' => 'string',
            'category' => 'required|integer',
            'user' => 'required|integer',
            'status' => 'required|string'
        ]);

        $article = Article::firstOrCreate([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $request->image,
            'category_id' => $request->category,
            'user_id' => $request->user,
            'status' => $request->status
        ]);

        return response()->json($article, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        $article = DB::table('articles')
            ->join('categories', 'articles.category_id', '=', 'categories.id')
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->select('articles.id', 'articles.title', 'articles.content', 'articles.image', 'categories.name AS category', 'articles.created_at', 'users.name as user')
            ->where('articles.id', '=', $article->id)
            ->first();

        $article->created_at = date('d F Y', strtotime($article->created_at));

        $data = array("status" => 200, "results" => $article);

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'required|string|unique:articles,title,' . $article->id,
            'content' => 'required|string',
            'image' => 'string',
            'category' => 'required|integer',
            'user' => 'required|integer',
            'status' => 'required|string'
        ]);

        $article->update([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $request->image,
            'category_id' => $request->category,
            'user_id' => $request->user,
            'status' => $request->status
        ]);

        return response()->json($article);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return response()->json('Article deleted successfully');
    }

    /**
     * Display the comments of a post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function comments($id)
    {
        $comments = Article::find($id)->comments()->select('id', 'content', 'created_at')->orderBy('created_at', 'DESC')->get();

        foreach ($comments as $key => $value) {
            $created_at = $value['created_at']->format('d F Y');
            unset($comments[$key]['created_at']);
            $comments[$key]['date'] = $created_at;
        }

        $data = array("status" => 200, "results" => $comments);

        return response()->json($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function recent($count)
    {
        $articles = DB::table('articles')
        ->join('categories', 'articles.category_id', '=', 'categories.id')
        ->join('users', 'articles.user_id', '=', 'users.id')
        ->select('articles.id', 'articles.title', 'articles.created_at', 'users.name as author')
        ->limit($count)
        ->orderBy('created_at', 'DESC')->get();

        foreach ($articles as $key => $value) {
            $articles[$key]->created_at = date('d F Y', strtotime($value->created_at));
            $articles[$key]->title = str_limit($value->title, 16);
        }

        $data = array("status" => 200, "results" => $articles);

        return response()->json($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function countArticles()
    {
        $articles_count = DB::table('articles')->count();
        $data = array("status" => 200, "results" => $articles_count);

        return response()->json($data);
    }
}
