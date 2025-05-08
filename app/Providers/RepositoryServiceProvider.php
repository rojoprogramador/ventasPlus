<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\IUserRepository;
use App\Repositories\UserRepository;
use App\Interfaces\IBaseRepository;
use App\Repositories\BaseRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Registrar repositorios base
        $this->app->bind(IBaseRepository::class, BaseRepository::class);
        
        // Registrar repositorios específicos
        $this->app->bind(IUserRepository::class, UserRepository::class);
        
        // Aquí se registrarán los nuevos repositorios
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
