<?php
namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */

    public function view(User $user, Article $article): bool
    {
        if ($article->status === 'published') {
            return true;
        }
        return $user->id === $article->user_id || $user->hasRole('admin') || $user->hasRole('editor');
    }

    /**
     * Determine whether the user can create models.
     */

    public function create(User $user): bool
    {
        return $user->hasRole('author') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */

    public function update(User $user, Article $article): bool
    {
        if ($user->id === $article->user_id && $user->hasRole('author')) {
            return true;
        }

        return $user->hasRole('admin') || $user->hasRole('editor');
    }

    /**
     * Determine whether the user can delete the model.
     */

    public function delete(User $user, Article $article): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */

    public function publish(User $user, Article $article)
    {
        return $user->hasAnyRole(['editor', 'admin']);
    }
}
