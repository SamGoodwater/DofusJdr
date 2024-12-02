<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        \App\Models\Page::class => \App\Policies\PagePolicy::class,
        \App\Models\Section::class => \App\Policies\SectionPolicy::class,
        \App\Models\Item::class => \App\Policies\ItemPolicy::class,

        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
