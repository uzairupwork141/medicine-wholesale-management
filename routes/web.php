<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest routes
|--------------------------------------------------------------------------
|
| Only guests can access the login page/form. Authenticated users are
| redirected away from these routes by Laravel's guest middleware.
|
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
|
| Every application page below requires a valid login and an active account.
| The "active" middleware also logs out a user whose account was disabled
| while they already had an active session.
|
*/
Route::middleware(['auth', 'active'])->group(function () {

    // Dashboard
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Customer routes
    |--------------------------------------------------------------------------
    |
    | Customers are available to authenticated admin and seller users.
    |
    */
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    /*
    |--------------------------------------------------------------------------
    | Admin-only routes
    |--------------------------------------------------------------------------
    |
    | Users, categories, companies, medicines and batches contain management
    | operations and are restricted at the server level to admins.
    |
    */
    Route::middleware('admin')->group(function () {

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Companies / manufacturers
        Route::get('/manufacturers', [ManufacturerController::class, 'index'])->name('manufacturers.index');
        Route::get('/manufacturers/create', [ManufacturerController::class, 'create'])->name('manufacturers.create');
        Route::post('/manufacturers', [ManufacturerController::class, 'store'])->name('manufacturers.store');
        Route::get('/manufacturers/{id}/edit', [ManufacturerController::class, 'edit'])->name('manufacturers.edit');
        Route::put('/manufacturers/{id}', [ManufacturerController::class, 'update'])->name('manufacturers.update');
        Route::delete('/manufacturers/{id}', [ManufacturerController::class, 'destroy'])->name('manufacturers.destroy');

        // Medicines
        Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines.index');
        Route::get('/medicines/create', [MedicineController::class, 'create'])->name('medicines.create');
        Route::post('/medicines', [MedicineController::class, 'store'])->name('medicines.store');
        Route::get('/medicines/{id}/edit', [MedicineController::class, 'edit'])->name('medicines.edit');
        Route::put('/medicines/{id}', [MedicineController::class, 'update'])->name('medicines.update');
        Route::delete('/medicines/{id}', [MedicineController::class, 'destroy'])->name('medicines.destroy');

        // Batches
        Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
        Route::get('/batches/create', [BatchController::class, 'create'])->name('batches.create');
        Route::post('/batches', [BatchController::class, 'store'])->name('batches.store');
        Route::get('/batches/{batch}/edit', [BatchController::class, 'edit'])->name('batches.edit');
        Route::put('/batches/{batch}', [BatchController::class, 'update'])->name('batches.update');
        Route::delete('/batches/{batch}', [BatchController::class, 'destroy'])->name('batches.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Sales
    |--------------------------------------------------------------------------
    | Sales are available to both admin and seller users.
    | Editing/deleting is restricted inside the controller to admin users.
    |--------------------------------------------------------------------------
    */
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/customers/search', [SaleController::class, 'searchCustomers'])->name('sales.customers.search');
    Route::get('/sales/medicines/search', [SaleController::class, 'searchMedicines'])->name('sales.medicines.search');
    Route::get('/sales/medicines/{medicine}/batches', [SaleController::class, 'batches'])->name('sales.medicines.batches');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');

    Route::middleware('admin')->group(function () {
        Route::get('/sales/{sale}/edit', [SaleController::class, 'edit'])->name('sales.edit');
        Route::put('/sales/{sale}', [SaleController::class, 'update'])->name('sales.update');
        Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    |
    | Logout is only valid for an authenticated user.
    |
    */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
