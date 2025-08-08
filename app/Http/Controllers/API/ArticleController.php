<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        try {
            $articles = Article::where('status', 'published')->with('author:id,name')->paginate(10);
            // dd($articles);
            return response()->json([
                'status'      => 'success',
                'status_code' => 200,
                'data'        => $articles,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to load articles',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function mine(Request $request)
    {
        try {
            $user = $request->user();
            // dd($user);
            $articles = Article::where('user_id', $user->id)->paginate(10);
            return response()->json([
                'status'      => 'success',
                'status_code' => 200,
                'data'        => $articles,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to load your articles',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function store(StoreArticleRequest $request)
    {
        try {
            $this->authorize('create', Article::class);

            $validated = $request->validated();

            $article               = new Article($validated);
            $article->user_id      = $request->user()->id;
            $article->published_at = now();
            $article->status       = 'draft';
            $article->save();

            return response()->json([
                'status'      => 'success',
                'status_code' => 201,
                'data'        => $article,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create article',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function update(UpdateArticleRequest $request, $id)
    {
        try {
            $article = Article::findOrFail($id);
            $this->authorize('update', $article);

            $article->update($request->validated());

            return response()->json([
                'status'      => 'success',
                'status_code' => 200,
                'data'        => $article,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update article',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $article = Article::findOrFail($id);
            $this->authorize('delete', $article);
            $article->delete();

            return response()->json([
                'status'      => 'success',
                'status_code' => 200,
                'message'     => 'Article deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete article',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function publish(Request $request, $id)
    {
        try {
            $article = Article::findOrFail($id);
            $this->authorize('publish', $article);

            $article->status       = 'published';
            $article->published_at = now();
            $article->save();

            return response()->json([
                'status'      => 'success',
                'status_code' => 200,
                'data'        => $article,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to publish article',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
