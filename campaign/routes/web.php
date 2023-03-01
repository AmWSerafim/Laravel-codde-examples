<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CampaignSController;
use App\Http\Controllers\CampaignFController;
use App\Http\Controllers\GeosController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\OptionController;

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

Route::get('/', [HomeController::class, 'index'])
    ->name('home');
Route::get('/dashboard', [HomeController::class, 'dashboard'])
    ->middleware('auth')
    ->name('dashboard');
/* admin routes */
Route::get('/admin', [AdminController::class, 'index'])
    ->middleware('auth')
    ->name('admin');

/* static campaign creation */
Route::get('/campaign-static', [CampaignSController::class, 'index'])
    ->middleware('auth')
    ->name('campaign_s');

Route::get('/campaign-static/create', [CampaignSController::class, 'create'])
    ->middleware('auth')
    ->name('campaign_s.create');
Route::get('/campaign-static/delete', [CampaignSController::class, 'delete'])
    ->middleware('auth')
    ->name('campaign_s.delete');
Route::get('/campaign-static/deleteAllCompanies', [CampaignSController::class, 'deleteAllCompanies'])
    ->middleware('auth')
    ->name('campaign_s.deleteAllCompanies');

Route::get('/campaign-static/showAllBudgets', [CampaignSController::class, 'showAllBudgets'])
    ->middleware('auth')
    ->name('campaign_s.showAllBudgets');
Route::get('/campaign-static/deleteAllBudgets', [CampaignSController::class, 'deleteAllBudgets'])
    ->middleware('auth')
    ->name('campaign_s.deleteAllBudgets');

Route::get('/campaign-static/showAll', [CampaignSController::class, 'showAll'])
    ->middleware('auth')
    ->name('campaign_s.showAll');

/* form campaign creation */
Route::get('/campaign-creation', [CampaignFController::class, 'index'])
    ->middleware('auth')
    ->name('campaign_f');
Route::post('/campaign-creation/preview', [CampaignFController::class, 'preview'])
    ->middleware('auth')
    ->name('campaign_f.preview');
Route::post('/campaign-creation/generate', [CampaignFController::class, 'generate'])
    ->middleware('auth')
    ->name('campaign_f.generate');
Route::get('/campaign-creation/generateCountriesPart', [CampaignFController::class, 'generateCountriesPart'])
    ->name('campaign_f.generateCountriesPart');
Route::get('/campaign-creation/getCustomAudience', [CampaignFController::class, 'getCustomAudience'])
    ->middleware('auth')
    ->name('campaign_f.getCustomAudience');
Route::get('/campaign-creation/getPrefixesLength', [CampaignFController::class, 'getPrefixesLength'])
    ->middleware('auth')
    ->name('campaign_f.getPrefixesLength');
Route::get('/campaign-creation/getCustomAudienceOutbrain', [CampaignFController::class, 'getCustomAudienceOutbrain'])
    ->middleware('auth')
    ->name('campaign_f.getCustomAudienceOutbrain');
Route::get('/campaign-creation/campaignAccounts', [CampaignFController::class, 'campaignAccounts'])
    ->middleware('auth')
    ->name('campaign_f.campaignAccounts');

/* geos routes */
Route::get('/geos/outbrainSearchAjax', [GeosController::class, 'outbrainCountrySearchAjax'])
    ->middleware('auth')
    ->name('geos.outbrainSearchAjax');
Route::resource('geos', GeosController::class)->middleware('role:admin');

/* Language routes*/
Route::resource('languages', LanguageController::class)->middleware('role:admin');

/* Websites routes*/
Route::resource('websites', WebsiteController::class)->middleware('role:admin');

/* Accounts routes*/
Route::get('/accounts/generateAccountsSelectAjax', [AccountController::class, 'generateAccountsSelect'])
    ->middleware('auth')
    ->name('accounts.generateAccountsSelectAjax');
Route::resource('accounts', AccountController::class)->middleware('role:admin');

/* Options routes*/
Route::resource('options', OptionController::class)->middleware('role:admin');

