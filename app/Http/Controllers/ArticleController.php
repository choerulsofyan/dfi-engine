<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;
use App\Traits\UploadTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use DB;

class ArticleController extends Controller
{
    use UploadTrait;

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
            $articles[$key]->content = str_limit($value->content, 230);
        }

        $data = array("status" => 200, "results" => $articles);

        return response()->json($data);
    }

    public function indexPublic()
    {
        $articles = DB::table('articles')
        ->join('categories', 'articles.category_id', '=', 'categories.id')
        ->join('users', 'articles.user_id', '=', 'users.id')
        ->select('articles.id', 'articles.title', 'articles.content', 'articles.image', 'categories.name AS category', 'articles.created_at', 'users.name as author', 'articles.status')
        ->where('articles.status', '=', 'PUBLISHED')
        ->orderBy('created_at', 'DESC')->get();

        foreach ($articles as $key => $value) {
            $articles[$key]->created_at = date('d F Y', strtotime($value->created_at));
            $articles[$key]->title = str_limit($value->title, 16);
            $articles[$key]->content = str_limit($value->content, 230);
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
        $data = json_decode($request->data, true);

        $rules = [
            'title' => 'required|string|unique:articles',
            'content' => 'required|string',
            'category' => 'required|integer',
            'user' => 'required|integer',
            'status' => 'required|string'
        ];

        $validator_data = Validator::make($data, $rules);

        $article_image = "no-image.png";

        if ($validator_data->passes()) {

            if ($request->has('image')) {

                $validator_image = Validator::make(array("image" => $request->image), [
                    'image' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
                ]);

                if ($validator_image->passes()) {
                    // Get image file
                    $image = $request->file('image');

                    // Make a image name based on user name and current timestamp
                    $name = str_slug($data['title']).'_'.time();

                    // Define folder path
                    $folder = '/assets/images/articles/';

                    // Make a file path where image will be stored [ folder path + file name + file extension]
                    // $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
                    $file_name = $name. '.' . $image->getClientOriginalExtension();

                    // Upload image
                    $this->uploadOne($image, $folder, 'public', $name);

                    // Set user profile image path in database to filePath
                    $article_image = $file_name;
                } else {
                    return response()->json($validator_image->errors()->all());
                }
            }

        } else {
            return response()->json($validator_data->errors()->all());
        }

        $article = Article::firstOrCreate([
            'title' => $data['title'],
            'content' => $data['content'],
            'image' => $article_image,
            'category_id' => $data['category'],
            'user_id' => $data['user'],
            'status' => $data['status']
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

    public function showPublic(Article $article)
    {
        $article = DB::table('articles')
            ->join('categories', 'articles.category_id', '=', 'categories.id')
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->select('articles.id', 'articles.title', 'articles.content', 'articles.image', 'categories.name AS category', 'articles.created_at', 'users.name as user')
            ->where('articles.id', '=', $article->id)
            ->where('articles.status', '=', 'PUBLISHED')
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
        $data = json_decode($request->data, true);

        $rules = [
            'title' => 'required|string|unique:articles,title,' . $article->id,
            'content' => 'required|string',
            'category' => 'required|integer',
            'user' => 'required|integer',
            'status' => 'required|string'
        ];

        $validator_data = Validator::make($data, $rules);

        $article_image = "no-image.png";

        if ($validator_data->passes()) {

            if ($request->has('image')) {

                $validator_image = Validator::make(array("image" => $request->image), [
                    'image' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
                ]);

                if ($validator_image->passes()) {
                    // Get image file
                    $image = $request->file('image');

                    // Make a image name based on user name and current timestamp
                    $name = str_slug($data['title']).'_'.time();

                    // Define folder path
                    $folder = '/assets/images/articles/';

                    // Make a file path where image will be stored [ folder path + file name + file extension]
                    // $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
                    $file_name = $name. '.' . $image->getClientOriginalExtension();

                    // Upload image
                    $this->uploadOne($image, $folder, 'public', $name);

                    // find old image name
                    $article_old_image = Article::select('image')->where('id', $article->id)->first()->image;
                    
                    // delete old image if image is not no-image.png
                    if ($article_old_image != "no-image.png") {
                        $image_path = public_path() . $folder . $article_old_image;
                        if(File::exists($image_path)) {
                            File::delete($image_path);
                        }
                    }

                    // Set user profile image path in database to filePath
                    $article_image = $file_name;
                } else {
                    return response()->json($validator_image->errors()->all());
                }
            }

        } else {
            return response()->json($validator_data->errors()->all());
        }

        /* $article = Article::firstOrCreate([
            'title' => $data['title'],
            'content' => $data['content'],
            'image' => $article_image,
            'category_id' => $data['category'],
            'user_id' => $data['user'],
            'status' => $data['status']
        ]);

        return response()->json($article, 201); */

        $article->update([
            'title' => $data['title'],
            'content' => $data['content'],
            'image' => $article_image,
            'category_id' => $data['category'],
            'user_id' => $data['user'],
            'status' => $data['status']
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

    public function category($id, $offset, $limit)
    {
        if (!$offset) {
            $offset = 0;
        }

        if (!$limit) {
            $limit = 12;
        }

        $articles = DB::table('articles')
        ->join('categories', 'articles.category_id', '=', 'categories.id')
        ->join('users', 'articles.user_id', '=', 'users.id')
        ->select('articles.id', 'articles.title', 'articles.content', 'articles.image', 'categories.name AS category', 'articles.created_at', 'users.name as author', 'articles.status')
        ->where('articles.category_id', '=', $id)
        ->where('articles.status', '=', 'PUBLISHED')
        ->offset($offset)
        ->limit($limit)
        ->orderBy('created_at', 'DESC')->get();

        foreach ($articles as $key => $value) {
            $articles[$key]->created_at = date('d F Y', strtotime($value->created_at));
            $articles[$key]->title = str_limit($value->title, 16);
            $articles[$key]->content = str_limit($value->content, 230);
        }

        $data = array("status" => 200, "results" => $articles);

        return response()->json($data);
    }
}
