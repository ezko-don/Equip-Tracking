<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\View\Components\AdminLayout;
use Illuminate\Support\Facades\Gate;
use App\Http\Middleware\AdminMiddleware;
use App\Services\Admin\AdminService;
use App\View\Components\ChatNotificationComponent;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('admin', function ($app) {
            return new AdminService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register admin-specific Blade components
        Blade::component('admin-layout', AdminLayout::class);

        // Update the admin Blade directive
        Blade::if('admin', function () {
            return app(AdminService::class)->check();
        });

        // Define admin gate
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });

        // Register the chat notification component
        Blade::component('chat-notification', ChatNotificationComponent::class);

        // Share variables with all views
        View::composer('*', function ($view) {
            // Default values
            $unreadMessages = 0;
            $unreadNotifications = 0;

            if (Auth::check()) {
                $user = Auth::user();
                
                try {
                    // Get unread messages count safely
                    if (Schema::hasTable('user_chats')) {
                        $unreadMessages = $user->unreadChats()->count() ?? 0;
                    }
                    
                    // Get unread notifications count safely
                    if (Schema::hasTable('notifications')) {
                        $unreadNotifications = $user->unreadNotifications()->count() ?? 0;
                    }
                } catch (\Exception $e) {
                    // Log error if needed
                    // \Log::error('Error getting unread counts: ' . $e->getMessage());
                }
            }
            
            $view->with([
                'unreadMessages' => $unreadMessages,
                'unreadNotifications' => $unreadNotifications
            ]);
        });
    }
}
