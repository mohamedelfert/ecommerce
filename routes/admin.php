<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {

    Config::set('auth.defines', 'admin');
    Route::get('login', 'AdminAuthController@login');
    Route::post('login', 'AdminAuthController@doLogin');
    Route::get('forgot/password', 'AdminAuthController@forgot_password');
    Route::post('forgot/password', 'AdminAuthController@doForgot_password');
    Route::get('reset/password/{token}', 'AdminAuthController@reset_password');
    Route::post('reset/password/{token}', 'AdminAuthController@doReset_password');

    Route::group(['middleware' => 'admin:admin'], function () {
        /**************************** Admins *******************************/
        Route::resource('admin', 'AdminsController');
        Route::delete('admin/destroy/all', 'AdminsController@delete_all');

        /**************************** Users *******************************/
        Route::resource('user', 'UsersController');
        Route::delete('user/destroy/all', 'UsersController@delete_all');

        /**************************** Countries *******************************/
        Route::resource('countries', 'CountryController');
        Route::delete('countries/destroy/all', 'CountryController@delete_all');

        /**************************** Cities *******************************/
        Route::resource('cities', 'CityController');
        Route::delete('cities/destroy/all', 'CityController@delete_all');

        /**************************** States *******************************/
        Route::resource('states', 'StateController');
        Route::delete('states/destroy/all', 'StateController@delete_all');

        /**************************** Departments *******************************/
        Route::resource('departments', 'DepartmentController');

        /**************************** TradeMarks *******************************/
        Route::resource('trademarks', 'TradeMarkController');
        Route::delete('trademarks/destroy/all', 'TradeMarkController@delete_all');

        /**************************** Manufacturers *******************************/
        Route::resource('manufacturers', 'ManufacturerController');
        Route::delete('manufacturers/destroy/all', 'ManufacturerController@delete_all');

        /**************************** Shipping Companies *******************************/
        Route::resource('companies', 'ShippingCompanyController');
        Route::delete('companies/destroy/all', 'ShippingCompanyController@delete_all');

        /**************************** Malls *******************************/
        Route::resource('malls', 'MallController');
        Route::delete('malls/destroy/all', 'MallController@delete_all');

        /**************************** Colors *******************************/
        Route::resource('colors', 'ColorController');
        Route::delete('colors/destroy/all', 'ColorController@delete_all');

        /**************************** Sizes *******************************/
        Route::resource('sizes', 'SizeController');
        Route::delete('sizes/destroy/all', 'SizeController@delete_all');

        /**************************** Settings *******************************/
        Route::get('setting', 'SettingsController@setting');
        Route::post('setting', 'SettingsController@setting_save');

        Route::get('/', function () {
            return view('admin.home');
        });

        Route::any('logout', 'AdminAuthController@logout');
    });

    /**************************** this route for change language *******************************/
    Route::get('lang/{lang}', function ($lang) {
        session()->has('lang') ? session()->forget('lang') : '';
        $lang == 'ar' ? session()->put('lang', 'ar') : session()->put('lang', 'en');
        return back();
    });

});
