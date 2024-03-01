<?php

use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/training-plan-chart-data', 'App\Http\Controllers\ChartController@trainingPlanChartData');
Route::get('/completion-chart-data', 'App\Http\Controllers\ChartController@completionChartData');
Route::get('/current-progress-data', 'App\Http\Controllers\ChartController@currentProgressData');
Route::get('/current-progress-data-trainer', 'App\Http\Controllers\ChartController@currentProgressDataTrainer');


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    
    Route::get('training-records/view', '\App\Http\Controllers\TrainingRecords@index')->middleware('admin.user')->name('training.record.form');
    Route::post('training-records/store', '\App\Http\Controllers\TrainingRecords@store')->name('training-records.store');
    Route::post('/training-records', '\App\Http\Controllers\TrainingRecords@store')->name('training-records.store');

    Route::get('/training-qr/{id}','\App\Http\Controllers\TrainingScheduleController@showQR')->name('training.qr.show');
    Route::get('/training-schedule/qr', '\App\Http\Controllers\TrainingScheduleController@index')->name('training.schedule');
});
