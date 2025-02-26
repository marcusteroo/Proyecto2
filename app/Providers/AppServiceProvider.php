<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Kanban;
use App\Observers\KanbanObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Kanban::observe(KanbanObserver::class);
    }
}
