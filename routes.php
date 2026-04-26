<?php

use Illuminate\Support\Facades\Route;

// Customer-facing routes (web auth)
Route::group([
    'middleware' => ['web', 'auth'],
    'namespace'  => '\Acelle\Server\Controllers',
    'prefix'     => 'plugins/acelle/server',
    'as'         => 'acelle_server.',
], function () {

    // Landing page (verify form + file upload from logged-in customer)
    Route::match(['get', 'post'], 'verify/email', 'LandingPageController@verify')->name('landing.verify');
    Route::post('verify/upload',                  'LandingPageController@upload')->name('landing.upload');
    Route::get('verify/upload/progress',          'LandingPageController@progress')->name('landing.upload_progress');
    Route::get('verify/upload/progress-json',     'LandingPageController@progressJson')->name('landing.upload_progress_json');
    Route::get('verify/upload/report',            'LandingPageController@report')->name('landing.upload_report');

    // Validate (bulk verification campaigns)
    Route::get('validate',                                                  'ValidateController@index')->name('validate.index');
    Route::get('validate/list',                                             'ValidateController@list')->name('validate.list');
    Route::get('validate-list',                                             'ValidateController@validate_list')->name('validate.legacy_list');
    Route::get('validate-bulk',                                             'ValidateController@validate_bulk')->name('validate.bulk');
    Route::match(['get', 'post'], 'validate/verify',                        'ValidateController@verify')->name('validate.verify');
    Route::post('validate/upload',                                          'ValidateController@upload')->name('validate.upload');
    Route::post('validate/import',                                          'ValidateController@import')->name('validate.import');
    Route::post('validate/bulk/save',                                       'ValidateController@validateBulkSave')->name('validate.bulk_save');
    Route::get('validate/{verification_campaign_uid}/validate/bulk/result', 'ValidateController@validateBulkResult')->name('validate.bulk_result');
    Route::get('validate/{uid}/progress',                                   'ValidateController@progress')->name('validate.progress');
    Route::get('validate/{verification_campaign_uid}/progress/json',        'ValidateController@progressJson')->name('validate.progress_json');
    Route::get('validate/{uid}/full-progress',                              'ValidateController@fullProgress')->name('validate.full_progress');
    Route::get('validate/{uid}/full-progress/json',                         'ValidateController@fullProgressJson')->name('validate.full_progress_json');
    Route::get('validate/{uid}/full-progress/emails',                       'ValidateController@fullProgressEmails')->name('validate.full_progress_emails');
    Route::get('validate/emails',                                           'ValidateController@emailsList')->name('validate.emails_list');
    Route::post('validate/{uid}/start',                                     'ValidateController@start')->name('validate.start');
    Route::post('validate/{uid}/restart',                                   'ValidateController@restart')->name('validate.restart');
    Route::post('validate/{uid}/pause',                                     'ValidateController@pause')->name('validate.pause');
    Route::post('validate/{uid}/delete',                                    'ValidateController@delete')->name('validate.delete');
    Route::get('validate/{verification_campaign_uid}/report',               'ValidateController@report')->name('validate.report');

    // Monitor (browse verified emails by status)
    Route::get('monitor/{tab?}',                                            'MonitorController@index')->name('monitor.index');
    Route::get('monitor-list/{tab?}',                                       'MonitorController@list')->name('monitor.list');
});

// API — uses Acelle core's TokenGuard (auth:api). Pass token via:
//   ?api_token=<users.api_token>             (query string)
//   POST body field api_token=...
//   Authorization: Bearer <api_token>        (header)
Route::group([
    'middleware' => ['auth:api'],
    'namespace'  => '\Acelle\Server\Controllers\Api',
    'prefix'     => 'plugins/acelle/server/api/v1',
    'as'         => 'acelle_server.api.',
], function () {
    Route::match(['get', 'post'], 'verify',         'EmailVerificationController@verify')->name('verify');
    Route::post('batch-verify',                     'EmailVerificationController@batchVerify')->name('batch_verify');
    Route::match(['get', 'post'], 'batch-status',   'EmailVerificationController@batchStatus')->name('batch_status');
    Route::post('batch-result',                     'EmailVerificationController@batchResult')->name('batch_result');
    Route::get('get-credits',                       'EmailVerificationController@getCredits')->name('get_credits');
    Route::get('test',                              'EmailVerificationController@test')->name('test');
});
