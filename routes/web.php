<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Api\UserEventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\DashboardController as AdminPanelController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\EquipmentController as AdminEquipmentController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\DashboardController as UserDashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FirstTimePasswordController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Contact Routes
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// Guest Routes
Route::middleware('guest')->group(function () {
    // Removed registration routes - only admins can register users
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// First-time password change routes
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [FirstTimePasswordController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [FirstTimePasswordController::class, 'changePassword'])->name('password.change.store');
});

// Regular user routes (must be placed BEFORE admin routes)
Route::middleware(['auth', \App\Http\Middleware\CheckPasswordChanged::class])->group(function () {
    // User Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Equipment routes
    Route::get('/equipment', [EquipmentController::class, 'userIndex'])->name('equipment.index');
    Route::get('/equipment/{equipment}', [EquipmentController::class, 'show'])->name('equipment.show');
    
    // User Booking routes
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/create/{equipment?}', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
        Route::patch('/{booking}/approve', [BookingController::class, 'approve'])->name('approve');
        Route::patch('/{booking}/reject', [BookingController::class, 'reject'])->name('reject');
        Route::patch('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        Route::patch('/{booking}/complete', [BookingController::class, 'complete'])->name('complete');
        Route::patch('/{booking}/return', [BookingController::class, 'return'])->name('return');
        Route::post('/{booking}/return', [BookingController::class, 'return']);
        Route::delete('/clear-all', [BookingController::class, 'clearAll'])->name('clear-all');
        Route::delete('/{booking}', [BookingController::class, 'destroy'])->name('destroy');
    });

    // User Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');

    // Tasks Routes
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::get('/calendar', [TaskController::class, 'calendar'])->name('calendar');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/create', [TaskController::class, 'create'])->name('create');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::patch('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
    });

    // Categories Routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

    // Notifications Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::patch('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });

    // Messages Routes
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/inbox', [MessageController::class, 'inbox'])->name('inbox');
        Route::get('/{message}', [MessageController::class, 'show'])->name('show');
        Route::post('/{message}/reply', [MessageController::class, 'reply'])->name('reply');
        Route::post('/send', [MessageController::class, 'send'])->name('send');
    });

    // API Routes
    Route::prefix('api')->group(function () {
        Route::get('/users/search', [MessageController::class, 'searchUsers']);
        Route::get('/messages', [MessageController::class, 'getMessages']);
        Route::post('/messages', [MessageController::class, 'store']);
        Route::post('/messages/mark-read', [MessageController::class, 'markAsRead']);
        Route::get('/messages/unread-count', [MessageController::class, 'getUnreadCount']);
    });

    // Chat Routes
    Route::prefix('chat')->group(function () {
        Route::get('/messages', [ChatController::class, 'getMessages']);
        Route::post('/send', [ChatController::class, 'sendMessage']);
    });
});

// Admin routes
Route::middleware(['auth', \App\Http\Middleware\CheckPasswordChanged::class, \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/', [AdminPanelController::class, 'index'])->name('home');
        Route::get('/dashboard', [AdminPanelController::class, 'index'])->name('dashboard');
        
        // Equipment Management
        Route::resource('equipment', AdminEquipmentController::class);
        Route::patch('equipment/{equipment}/status', [AdminEquipmentController::class, 'updateStatus'])->name('equipment.update-status');
        Route::patch('equipment/{equipment}/condition', [AdminEquipmentController::class, 'updateCondition'])->name('equipment.update-condition');

        // User Management
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-role', [UserController::class, 'toggleRole'])->name('users.toggle-role');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // Category Management
        Route::resource('categories', AdminCategoryController::class);

        // Bookings Management
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [AdminBookingController::class, 'index'])->name('index');
            Route::get('/pending', [AdminBookingController::class, 'pending'])->name('pending');
            Route::get('/pending-returns', [AdminBookingController::class, 'pendingReturns'])->name('pending-returns');
            Route::get('/{booking}', [AdminBookingController::class, 'show'])->name('show');
            Route::patch('/{booking}/approve', [AdminBookingController::class, 'approve'])->name('approve');
            Route::patch('/{booking}/reject', [AdminBookingController::class, 'reject'])->name('reject');
            Route::patch('/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('cancel');
            Route::patch('/{booking}/complete', [AdminBookingController::class, 'complete'])->name('complete');
            Route::patch('/{booking}/return', [AdminBookingController::class, 'return'])->name('return');
            Route::post('/{booking}/return', [AdminBookingController::class, 'return']);
            Route::get('/{booking}/return', function(\App\Models\Booking $booking) {
                return redirect()->route('admin.bookings.show', $booking)
                    ->with('error', 'Equipment returns must be submitted through the return form.');
            });
            Route::delete('/{booking}', [AdminBookingController::class, 'destroy'])->name('destroy');
            Route::patch('/{booking}/approve-return', [AdminBookingController::class, 'approveReturn'])
                ->name('approve-return');
        });

        // Reports Routes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/equipment-usage', [ReportController::class, 'equipmentUsage'])->name('equipment-usage');
            Route::get('/condition-history', [ReportController::class, 'conditionHistory'])->name('condition-history');
            Route::get('/availability', [ReportController::class, 'availability'])->name('availability');
            Route::get('/booking-statistics', [ReportController::class, 'bookingStatistics'])->name('booking-statistics');
            Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
            Route::get('/download/{type}', [ReportController::class, 'download'])->name('download');
        });
        
        // Maintenance Routes
        Route::prefix('maintenance')->name('maintenance.')->group(function () {
            Route::get('/', [MaintenanceController::class, 'index'])->name('index');
            Route::post('/log', [MaintenanceController::class, 'store'])->name('store');
            Route::get('/{equipment}/history', [MaintenanceController::class, 'history'])->name('history');
        });

        // Maintenance Management
        Route::resource('maintenances', MaintenanceController::class);

        // Inside your admin route group
        Route::prefix('maintenances')->name('maintenances.')->group(function () {
            Route::get('/', [MaintenanceController::class, 'index'])->name('index');
            Route::get('/create', [MaintenanceController::class, 'create'])->name('create');
            Route::post('/', [MaintenanceController::class, 'store'])->name('store');
            // Add other maintenance routes as needed
        });

        // Task Management
        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::get('/', [TaskController::class, 'adminIndex'])->name('index'); 
            Route::get('/calendar', [TaskController::class, 'adminCalendar'])->name('calendar');
            Route::get('/{task}', [TaskController::class, 'show'])->name('show');
            Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
            Route::patch('/{task}', [TaskController::class, 'update'])->name('update');
            Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
        });

        // Debug route for testing
        Route::get('/debug/approve-return/{booking}', function(App\Models\Booking $booking) {
            return app()->make(App\Http\Controllers\Admin\BookingController::class)->approveReturn($booking);
        })->name('debug.approve-return');

        // Add these new routes
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/mark-as-read', [AdminNotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::post('/notifications/mark-all-as-read', [AdminNotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::get('/notifications/unread-count', [AdminNotificationController::class, 'getUnreadCount']);

        // Add messaging routes for admins
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/user/{user}', [MessageController::class, 'showConversation'])->name('messages.conversation');

        // Add message count route
        Route::get('/messages/unread-count', [MessageController::class, 'getUnreadCount']);
    });

// Add this if it doesn't exist already
Route::get('/messages', function () {
    return view('messages.index');
})->middleware(['auth'])->name('messages.index');

Route::post('/messages/send', 'App\Http\Controllers\MessageController@send')
    ->middleware(['auth'])
    ->name('messages.send');

require __DIR__.'/auth.php';
