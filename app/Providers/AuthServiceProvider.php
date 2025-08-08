<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Article;
use App\Policies\ArticlePolicy;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Article::class => ArticlePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

         Gate::define('view-users', function ($user) {
            return $user->hasPermission('view-users') || $user->hasRole('admin');
        });

        Gate::define('assign-roles', function ($user) {
            return $user->hasPermission('assign-roles') || $user->hasRole('admin');
        });

    }
}
