<?php

Route::get('quick-crud', 'Crankd\Quickcrud\QuickcrudController@create')->name('quickcrud.create');
Route::post('quick-crud/store', 'Crankd\Quickcrud\QuickcrudController@store')->name('quickcrud.store');
Route::get('quick-crud/redirect/{name}', 'Crankd\Quickcrud\QuickcrudController@redirect')->name('quickcrud.redirect');