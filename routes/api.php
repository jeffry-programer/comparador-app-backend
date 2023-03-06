<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\ProdController;
use Illuminate\Support\Facades\Route;

//Rutas para autenticacion
Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);

//Rutas para busqueda de productos
Route::post('search-products',[ProdController::class,'allProds']);
Route::get('prod-more-search',[ProdController::class,'queryProdsMoreSearch']);
Route::post('query-detail-prod',[ProdController::class,'queryDetailProd']);
Route::post('prod-sugerations',[ProdController::class,'productSugerations']);
Route::post('query-other-price',[ProdController::class,'queryOtherPrice']);
Route::post('query-prods-subcategory',[ProdController::class,'queryProdsBySubCategoryProd']);
Route::post('query-prods-list',[ProdController::class,'queryProdList']);

//Rutas protegidas con sanctum
Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('user-profile', [AuthController::class, 'userProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
});

//Rutas generales
Route::get('allCategories',[GeneralController::class,'allCategories']);
Route::get('query-bussiness',[GeneralController::class,'queryBussiness']);
Route::post('query-list',[GeneralController::class,'queryList']);
Route::post('query-sub-categories',[GeneralController::class,'querySubCategoriesByNameCategory']);
Route::post('query-detail-profile',[GeneralController::class,'queryDetailProfile']);
Route::post('query-notifications',[GeneralController::class,'queryNotifications']);
Route::post('change-state-notification',[GeneralController::class,'changeStateNotification']);
Route::post('query-notification-not-read',[GeneralController::class,'queryNotificatioNotRead']);
Route::post('query-detail-list',[GeneralController::class,'queryDetaiList']);
Route::post('add-prod-list',[GeneralController::class,'addProdList']);
Route::post('change-status-prod',[GeneralController::class,'changeStatusProd']);
Route::post('delete-prod-list',[GeneralController::class,'deleteProdList']);
Route::post('edit-prod-list',[GeneralController::class,'editProdList']);
Route::post('delete-list',[GeneralController::class,'deleteList']);
Route::post('add-list',[GeneralController::class,'addList']);
Route::post('share-list',[GeneralController::class,'shareList']);
Route::post('compare-list',[GeneralController::class,'compareList']);
Route::post('compare-list-bussiness',[GeneralController::class,'compareListBussiness']);
Route::post('save-profile',[GeneralController::class,'saveProfile']);
Route::post('save-info-search',[GeneralController::class,'saveInfoSearch']);
Route::post('change-password',[GeneralController::class,'changePassword']);


