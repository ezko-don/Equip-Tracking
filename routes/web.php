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
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Regular user routes (must be placed BEFORE admin routes)
Route::middleware(['auth'])->group(function () {
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
        Route::patch('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        Route::post('/return', [BookingController::class, 'return'])->name('return');
    });

    // User Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');

    // Tasks Routes
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('/tasks/calendar', [TaskController::class, 'calendar'])->name('tasks.calendar');

    // Regular User Routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::delete('/bookings/clear-all', [BookingController::class, 'clearAll'])
        ->name('bookings.clear-all');
    Route::resource('bookings', BookingController::class);
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::patch('/bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');

    // Add Notification Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::patch('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
    });

    // Admin booking routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::patch('/bookings/{booking}/cancel', [Admin\BookingController::class, 'cancel'])->name('bookings.cancel');
    });

    // New delete route
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');

    // New booking routes
    Route::get('/bookings/create/{equipment?}', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

    // Message routes
    Route::prefix('api')->group(function () {
        Route::get('/users/search', [MessageController::class, 'searchUsers']);
        Route::get('/messages', [MessageController::class, 'getMessages']);
        Route::post('/messages', [MessageController::class, 'store']);
        Route::post('/messages/mark-read', [MessageController::class, 'markAsRead']);
    });

    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
    });

    Route::get('/chat/messages', [ChatController::class, 'getMessages']);
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);

    // Messages Routes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/api/messages', [MessageController::class, 'getMessages']);
    Route::post('/api/messages', [MessageController::class, 'store']);
});

// Chat routes only for admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/api/messages/{userId}', [ChatController::class, 'getMessages']);
    Route::post('/api/messages', [ChatController::class, 'sendMessage']);
});

// Admin routes (keep existing admin routes)
Route::middleware(['auth', 'is_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/', [AdminPanelController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminPanelController::class, 'index'])->name('dashboard');
        
        // Equipment Management
        Route::resource('equipment', AdminEquipmentController::class);
        Route::patch('equipment/{equipment}/status', [AdminEquipmentController::class, 'updateStatus'])->name('equipment.update-status');
        Route::patch('equipment/{equipment}/condition', [AdminEquipmentController::class, 'updateCondition'])->name('equipment.update-condition');

        // User Management
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-role', [UserController::class, 'toggleRole'])->name('users.toggle-role');

        // Category Management
        Route::resource('categories', AdminCategoryController::class);

        // Bookings Management
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [AdminBookingController::class, 'index'])->name('index');
            Route::get('/pending', [AdminBookingController::class, 'pending'])->name('pending');
            Route::get('/{booking}', [AdminBookingController::class, 'show'])->name('show');
            Route::patch('/{booking}/approve', [AdminBookingController::class, 'approve'])->name('approve');
            Route::patch('/{booking}/reject', [AdminBookingController::class, 'reject'])->name('reject');
            Route::patch('/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('cancel');
            Route::patch('/{booking}/complete', [AdminBookingController::class, 'complete'])->name('complete');
            Route::patch('/{booking}/return', [AdminBookingController::class, 'return'])->name('return');
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

        // Add these new routes
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/mark-as-read', [AdminNotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::post('/notifications/mark-all-as-read', [AdminNotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::get('/notifications/unread-count', [AdminNotificationController::class, 'getUnreadCount']);
    });

    // Add message count route
    Route::get('/api/messages/unread-count', [MessageController::class, 'getUnreadCount']);

require __DIR__.'/auth.php';
