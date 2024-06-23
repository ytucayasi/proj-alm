<?php

use App\Livewire\Admin\Area\AreaPage;
use App\Livewire\Admin\Area\AreaProductPage;
use App\Livewire\Admin\Category\CategoryPage;
use App\Livewire\Admin\Category\CategoryShow;
use App\Livewire\Admin\Inventory\InventoryPage;
use App\Livewire\Admin\Product\ProductPage;
use App\Livewire\Admin\Product\ProductShow;
use App\Livewire\Admin\Report\ReportPage;
use App\Livewire\Admin\Reservation\ReservationCU;
use App\Livewire\Admin\Reservation\ReservationPage;
use App\Livewire\Admin\Unit\UnitPage;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('dashboard', Dashboard::class)
  ->middleware(['auth', 'verified'])
  ->name('dashboard');

Route::view('profile', 'profile')
  ->middleware(['auth'])
  ->name('profile');

Route::middleware(['auth', 'verified'])->group(function () {
  // Categorías
  Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', CategoryPage::class)->name('index');
    Route::get('/show/{categoryId}', CategoryShow::class)->name('show');
  });
  Route::prefix('units')->name('units.')->group(function () {
    Route::get('/', UnitPage::class)->name('index');
    Route::get('/{unitId}', UnitPage::class)->name('show');
  });
  Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', ProductPage::class)->name('index');
    Route::get('/{productId}', ProductShow::class)->name('show');
  });
  Route::prefix('areas')->name('areas.')->group(function () {
    Route::get('/', AreaPage::class)->name('index');
    Route::get('/{areaId}', AreaPage::class)->name('show');
    Route::get('/{areaId}/products', AreaProductPage::class)->name('products');
  });
  Route::prefix('inventories')->name('inventories.')->group(function () {
    Route::get('/', InventoryPage::class)->name('index');
    Route::get('/{inventoryId}', InventoryPage::class)->name('show');
  });
  Route::prefix('reservations')->name('reservations.')->group(function () {
    Route::get('/', ReservationPage::class)->name('index');
    Route::get('/form', ReservationCU::class)->name('form');
    Route::get('/{reservationId}', ReservationPage::class)->name('show');
  });
  Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', ReportPage::class)->name('index');
  });
});

require __DIR__ . '/auth.php';