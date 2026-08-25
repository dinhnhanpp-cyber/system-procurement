<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PriceSheetController;
use App\Http\Controllers\PricingRuleController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCostSettingController;
use App\Http\Controllers\SupplierController;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashboard');
    Route::redirect('/home', '/dashboard');

    Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');
    //ProductCategory
    Route::get('admin/productCategory/add',[ProductCategoryController::class, 'add']);
    Route::get('admin/productCategory/list',[ProductCategoryController::class, 'list']);
    Route::post('admin/productCategory/addStore',[ProductCategoryController::class, 'addStore'])->name('admin.productCategory.addStore');
    Route::get('admin/product-category/edit/{id}', [ProductCategoryController::class, 'edit'])->name('admin.productCategory.edit');
    Route::put('admin/productCategory/editStore/{id}', [ProductCategoryController::class, 'editStore'])->name('admin.productCategory.editStore');
    Route::delete('admin/productCategory/delete/{id}', [ProductCategoryController::class, 'delete'])->name('admin.productCategory.delete');
    // Supplier
    Route::get('admin/supplier/add',[SupplierController::class, 'add']);
    Route::get('admin/supplier/list',[SupplierController::class, 'list']);
    Route::post('admin/supplier/addStore',[SupplierController::class, 'addStore'])->name('admin.supplier.addStore');
    Route::get('admin/supplier/edit/{id}', [SupplierController::class, 'edit'])->name('admin.supplier.edit');
    Route::put('admin/supplier/editStore/{id}', [SupplierController::class, 'editStore'])->name('admin.supplier.editStore');
    Route::delete('admin/supplier/delete/{id}', [SupplierController::class, 'delete'])->name('admin.supplier.delete');
    // Product 
    Route::get('admin/product/add',[ProductController::class, 'add']);
    Route::get('admin/product/list',[ProductController::class, 'list']);
    Route::post('admin/product/addStore',[ProductController::class, 'addStore'])->name('admin.product.addStore');
    Route::get('admin/product/edit/{id}', [ProductController::class, 'edit'])->name('admin.product.edit');
    Route::put('admin/product/editStore/{id}', [ProductController::class, 'editStore'])->name('admin.product.editStore');
    Route::delete('admin/product/delete/{id}', [ProductController::class, 'delete'])->name('admin.product.delete');
    // ProductCostSetting
    Route::get('admin/productCostSetting/add',[ProductCostSettingController::class, 'add']);
    Route::get('admin/productCostSetting/list',[ProductCostSettingController::class, 'list']);
    Route::post('admin/productCostSetting/addStore',[ProductCostSettingController::class, 'addStore'])->name('admin.productCostSetting.addStore');
    Route::get('admin/productCostSetting/edit/{id}', [ProductCostSettingController::class, 'edit'])->name('admin.productCostSetting.edit');
    Route::put('admin/productCostSetting/editStore/{id}', [ProductCostSettingController::class, 'editStore'])->name('admin.productCostSetting.editStore');
    Route::delete('admin/productCostSetting/delete/{id}', [ProductCostSettingController::class, 'delete'])->name('admin.productCostSetting.delete');
    // PricingRule
    Route::get('admin/pricingRule/add',[PricingRuleController::class, 'add']);
    Route::get('admin/pricingRule/list',[PricingRuleController::class, 'list']);
    Route::post('admin/pricingRule/addStore',[PricingRuleController::class, 'addStore'])->name('admin.pricingRule.addStore');
    Route::get('admin/pricingRule/edit/{id}', [PricingRuleController::class, 'edit'])->name('admin.pricingRule.edit');
    Route::put('admin/pricingRule/editStore/{id}', [PricingRuleController::class, 'editStore'])->name('admin.pricingRule.editStore');
    Route::delete('admin/pricingRule/delete/{id}', [PricingRuleController::class, 'delete'])->name('admin.pricingRule.delete');
    // PriceSheet
    Route::get('admin/priceSheet/add',[PriceSheetController::class, 'add']);
    Route::get('admin/priceSheet/list',[PriceSheetController::class, 'list']);
    Route::post('admin/priceSheet/addStore',[PriceSheetController::class, 'addStore'])->name('admin.priceSheet.addStore');
    Route::get('admin/products/{id}/cost-settings', [PriceSheetController::class, 'getCostSettings']);
    // mới 
    Route::get('admin/priceSheet/detail/{id}', [PriceSheetController::class, 'detail'])->name('admin.priceSheet.detail');
Route::get('admin/priceSheet/edit/{id}', [PriceSheetController::class, 'edit'])->name('admin.priceSheet.edit');
Route::put('admin/priceSheet/editStore/{id}', [PriceSheetController::class, 'editStore'])->name('admin.priceSheet.editStore');
Route::delete('admin/priceSheet/delete/{id}', [PriceSheetController::class, 'delete'])->name('admin.priceSheet.delete');
Route::get('admin/priceSheet/all-details', [PriceSheetController::class, 'allDetailItems'])->name('admin.priceSheet.allDetails');
Route::get('admin/priceSheet/export-excel', [PriceSheetController::class, 'exportExcel'])->name('admin.priceSheet.exportExcel');
});

