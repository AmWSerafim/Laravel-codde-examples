<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MappingController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\UserController;

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

/*
Route::get('/', function () {
    return view('welcome');
});
*/
//Route::resource('projects', ProjectController::class)->name("*", "project");

/* excel test work */
/*
Route::get('/excel', [ExcelController::class, 'index'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('excel');

Route::post('/excel', [ExcelController::class, 'import'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('excel.import');
*/

/* dummy data filler */
Route::get('/seed-users', [App\Http\Controllers\DummyPermissionController::class, 'Permission'])->name('dummy_roles');

Auth::routes();

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

/* admin routes */
Route::get('/admin', [AdminController::class, 'index'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('admin');

/* mapping routes */
Route::get('/mapping', [MappingController::class, 'index'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user,create-mapping')
    ->name('mapping');

Route::get('/mapping/import', [MappingController::class, 'import'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user,create-mapping')
    ->name('mapping.import');

Route::post('/mapping/preview', [MappingController::class, 'preview'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('mapping.preview');

Route::get('/mapping/reimport/{id}', [MappingController::class, 'reimport'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('mapping.reimport');

Route::post('/mapping/create', [MappingController::class, 'create'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user,create-mapping')
    ->name('mapping.create');

Route::get('/mapping/download/{file}', [MappingController::class, 'download'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('mapping.download');

Route::delete('/mapping/{id}/delete', [MappingController::class, 'destroy'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('mapping.destroy');

Route::post('/mapping/report', [MappingController::class, 'report'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('mapping.report');

Route::post('/mapping/export', [MappingController::class, 'generate_report'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('mapping.export');

Route::get('/mapping/ready-exports/', [MappingController::class, 'ready_exports'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('mapping.ready_exports');

Route::get('/mapping/{id}/export_preview', [MappingController::class, 'export_preview'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('mapping.export_preview');

/* import routes */
Route::get('/import', [ImportController::class, 'index'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('import');

Route::get('/import/do-export/{id}', [ImportController::class, 'do_export'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('import.do_export');

Route::delete('/import/{id}/delete', [ImportController::class, 'destroy'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('import.destroy');

/* export routes */
Route::get('/import-history', [ExportController::class, 'index'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('import-history');

Route::get('/import-history/{id}/preview', [ExportController::class, 'preview'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin|company-user')
    ->name('import-history.preview');


/* user routes */
Route::get('/user', [UserController::class, 'index'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin')
    ->name('users');

Route::get('/users/create/company/{id}', [UserController::class, 'create'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin')
    ->name('users.create');

Route::post('/users/store', [UserController::class, 'store'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin')
    ->name('users.store');

Route::delete('/users/{id}/delete/company/{company_id}', [UserController::class, 'destroy'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin')
    ->name('users.destroy');

Route::get('/users/{id}/view', [UserController::class, 'show'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin')
    ->name('users.show');

Route::get('/users/{id}/edit', [UserController::class, 'edit'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin')
    ->name('users.edit');

Route::patch('/users/{id}/update', [UserController::class, 'update'])
    ->middleware('auth')
    ->middleware('role:master-admin|company-admin')
    ->name('users.update');


/* roles routes*/
Route::get('/role', [RoleController::class, 'index'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('roles');

Route::get('/role/create', [RoleController::class, 'create'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('roles.create');
Route::post('/role/store', [RoleController::class, 'store'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('roles.store');

Route::delete('/role/{id}/delete', [RoleController::class, 'destroy'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('roles.destroy');

Route::get('/role/{id}/view', [RoleController::class, 'show'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('roles.show');

Route::get('/role/{id}/edit', [RoleController::class, 'edit'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('roles.edit');
Route::patch('/role/{id}/update', [RoleController::class, 'update'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('roles.update');


/* permission routes */
Route::get('/permission', [PermissionController::class, 'index'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('permissions');

Route::get('/permission/create', [PermissionController::class, 'create'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('permissions.create');
Route::post('/permission/store', [PermissionController::class, 'store'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('permissions.store');

Route::delete('/permission/{id}/delete', [PermissionController::class, 'destroy'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('permissions.destroy');

Route::get('/permission/{id}/view', [PermissionController::class, 'show'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('permissions.show');

Route::get('/permission/{id}/edit', [PermissionController::class, 'edit'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('permissions.edit');
Route::patch('/permission/{id}/update', [PermissionController::class, 'update'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('permissions.update');


/* companies routes */
Route::get('/companies', [CompanyController::class, 'index'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('companies');

Route::get('/companies/create', [CompanyController::class, 'create'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('companies.create');

Route::post('/companies/store', [CompanyController::class, 'store'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('companies.store');

Route::delete('/companies/{id}/delete', [CompanyController::class, 'destroy'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('companies.destroy');

Route::get('/companies/{id}/view', [CompanyController::class, 'show'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('companies.show');

Route::get('/companies/{id}/edit', [CompanyController::class, 'edit'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('companies.edit');

Route::get('/companies/{id}/switch-to', [CompanyController::class, 'switch_to'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('companies.switch_to');

Route::get('/companies/switch-reset', [CompanyController::class, 'switch_reset'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('companies.switch_reset');

Route::patch('/companies/{id}/update', [CompanyController::class, 'update'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('companies.update');

Route::get('/companies/{id}/users', [CompanyController::class, 'users'])
    ->middleware('auth')
    ->middleware('role:master-admin')
    ->name('company.users');
