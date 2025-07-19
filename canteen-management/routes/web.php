<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Livewire\MenuDisplay;
use App\Livewire\CheckoutProcess;
use App\Livewire\OrderTracking;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');

// Menu and Shopping Routes
Route::get('/menu', MenuDisplay::class)->name('menu');
Route::get('/checkout', CheckoutProcess::class)->name('checkout');
Route::get('/track-order/{orderNumber?}', OrderTracking::class)->name('order.track');

// Order Confirmation Route
Route::get('/order/confirmation/{orderNumber}', function ($orderNumber) {
    $order = \App\Models\Order::where('order_number', $orderNumber)->firstOrFail();
    return view('order.confirmation', compact('order'));
})->name('order.confirmation');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Guest Routes (for non-authenticated users)
Route::middleware(['guest'])->group(function () {
    // Guests can browse menu and place online orders
    Route::get('/guest/menu', MenuDisplay::class)->name('guest.menu');
    Route::get('/guest/checkout', CheckoutProcess::class)->name('guest.checkout');
});

// Customer Routes (authenticated customers)
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/my-orders', function () {
        $orders = auth()->user()->orders()->latest()->paginate(10);
        return view('customer.orders', compact('orders'));
    })->name('customer.orders');
});

// API Routes for real-time updates
Route::middleware(['auth'])->group(function () {
    Route::post('/orders/{order}/update-status', function (\App\Models\Order $order) {
        // Only allow admins and tenants to update order status
        if (!auth()->user()->hasRole(['admin', 'tenant'])) {
            abort(403);
        }
        
        $request = request();
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,completed,cancelled'
        ]);
        
        $order->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);
        
        return response()->json(['success' => true]);
    })->name('orders.update-status');
});
