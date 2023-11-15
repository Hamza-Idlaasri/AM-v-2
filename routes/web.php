<?php

use Illuminate\Support\Facades\Route;

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

/********************************** Overview ***********************************************************************/

Route::get('/overview', App\Http\Livewire\Overview::class)->name('overview')->middleware('auth');

/*******************************************************************************************************************/

/********************************** Monitoring *********************************************************************/

Route::group(['prefix' => 'monitoring', 'namespace' => 'App\Http\Livewire\Monitoring\\', 'middleware' => 'auth'], function () {

    Route::get('/hosts', Hosts::class)->name('monitoring.hosts');
    Route::get('/hosts/{id}', Details\Host::class)->name('mh-details');

    Route::get('/services', Services::class)->name('monitoring.services');
    Route::get('/services/{id}', Details\Service::class)->name('ms-details');

    Route::get('/boxes', Boxes::class)->name('monitoring.boxes');
    Route::get('/boxes/{id}', Details\Box::class)->name('mb-details');

    Route::get('/equipements', Equips::class)->name('monitoring.equips');
    Route::get('/equipements/{id}', Details\Equip::class)->name('me-details');
});

/******************************************************************************************************************/

/********************************** Problems **********************************************************************/

Route::group(['prefix' => 'problems', 'namespace' => 'App\Http\Livewire\Problems\\', 'middleware' => 'auth'], function () {

    Route::get('/hosts', Hosts::class)->name('problems.hosts');
    Route::get('/services', Services::class)->name('problems.services');
    Route::get('/boxes', Boxes::class)->name('problems.boxes');
    Route::get('/equipements', Equips::class)->name('problems.equips');
});

/******************************************************************************************************************/

/*********************************** Config ***********************************************************************/

Route::group(['prefix' => 'config', 'middleware' => 'super_admin'], function () {

    /************************************ Groups **********************************************************************/
    Route::get('/hostgroups', App\Http\Livewire\Groups\Hosts::class);
    Route::get('/hostgroup', App\Http\Livewire\Groups\Details\Hostgroup::class)->name('hg-details');

    Route::get('/servicegroups', App\Http\Livewire\Groups\Services::class);
    Route::get('/servicegroups/{id}', App\Http\Livewire\Groups\Details\Servicegroup::class)->name('sg-details');

    Route::get('/boxgroups', App\Http\Livewire\Groups\Boxes::class);
    Route::get('/boxgroups/{id}', App\Http\Livewire\Groups\Details\Boxgroup::class)->name('bg-details');

    Route::get('/equipgroups', App\Http\Livewire\Groups\Equips::class);
    Route::get('/equipgroups/{id}', App\Http\Livewire\Groups\Details\Equipgroup::class)->name('eg-details');

    /*******************************************************************************************************************/

    //*********************************** Groups Config ***********************************************************************/
    // Add HG
    Route::get('/add-hostgroup', [App\Http\Controllers\Config\Add\Groups\Hostgroup::class, 'addHG'])->name('addHG');
    Route::get('/add-hostgroup/create', [App\Http\Controllers\Config\Add\Groups\Hostgroup::class, 'createHG'])->name('createHG');

    // Add SG
    Route::get('/add-servicegroup', [App\Http\Controllers\Config\Add\Groups\Servicegroup::class, 'addSG'])->name('addSG');
    Route::get('/add-servicegroup/create', [App\Http\Controllers\Config\Add\Groups\Servicegroup::class, 'createSG'])->name('createSG');

    // Add BG
    Route::get('/add-boxgroup', [App\Http\Controllers\Config\Add\Groups\Boxgroup::class, 'addBG'])->name('addBG');
    Route::get('/add-boxgroup/create', [App\Http\Controllers\Config\Add\Groups\Boxgroup::class, 'createBG'])->name('createBG');

    // Add EG
    Route::get('/add-equipgroup', [App\Http\Controllers\Config\Add\Groups\Equipgroup::class, 'addEG'])->name('addEG');
    Route::get('/add-equipgroup/create', [App\Http\Controllers\Config\Add\Groups\Equipgroup::class, 'createEG'])->name('createEG');

    // Edit HG
    Route::get('/manage-hostgroup/{id}', [App\Http\Controllers\Config\Groups\ManageHG::class, 'manageHG'])->name('manageHG');
    Route::get('/edit-hostgroup/{id}', [App\Http\Controllers\Config\Edit\Groups\Hostgroup::class, 'editHG'])->name('editHG');

    // Delete HG
    Route::get('/delete-hostgroup/{id}', [App\Http\Controllers\Config\Delete\Groups\Hostgroup::class, 'deleteHG'])->name('deleteHG');
    //-----------------------------------------------------------------------------------------------------------------------------------//

    // Edit BG
    Route::get('/manage-boxgroup/{id}', [App\Http\Controllers\Config\Groups\ManageBG::class, 'manageBG'])->name('manageBG');
    Route::get('/edit-boxgroup/{id}', [App\Http\Controllers\Config\Edit\Groups\Boxgroup::class, 'editBG'])->name('editBG');

    // Delete BG
    Route::get('/delete-boxgroup/{id}', [App\Http\Controllers\Config\Delete\Groups\Boxgroup::class, 'deleteBG'])->name('deleteBG');
    //-----------------------------------------------------------------------------------------------------------------------------------//

    // Edit SG
    Route::get('/manage-servicegroup/{id}', [App\Http\Controllers\Config\Groups\ManageSG::class, 'manageSG'])->name('manageSG');
    Route::get('/edit-servicegroup/{id}', [App\Http\Controllers\Config\Edit\Groups\Servicegroup::class, 'editSG'])->name('editSG');

    // Delete SG
    Route::get('/delete-servicegroup/{id}', [App\Http\Controllers\Config\Delete\Groups\Servicegroup::class, 'deleteSG'])->name('deleteSG');
    //-----------------------------------------------------------------------------------------------------------------------------------//

    // Edit EG
    Route::get('/manage-equipgroup/{id}', [App\Http\Controllers\Config\Groups\ManageEG::class, 'manageEG'])->name('manageEG');
    Route::get('/edit-equipgroup/{id}', [App\Http\Controllers\Config\Edit\Groups\Equipgroup::class, 'editEG'])->name('editEG');

    // Delete EG
    Route::get('/delete-equipgroup/{id}', [App\Http\Controllers\Config\Delete\Groups\Equipgroup::class, 'deleteEG'])->name('deleteEG');

    /******************************************************************************************************************/

    /************************************ Elements Config **********************************************************************/

    Route::get('/users', App\Http\Livewire\Auth\Config\Users::class)->name('config.users');

    /*** Display ***/
    Route::get('/hosts', App\Http\Livewire\Config\Display\Hosts::class)->name('config-hosts');
    Route::get('/boxes', App\Http\Livewire\Config\Display\Boxes::class)->name('config-boxes');
    Route::get('/services', App\Http\Livewire\Config\Display\Services::class)->name('config-services');
    Route::get('/pins', App\Http\Livewire\Config\Display\Pins::class)->name('config-pins');
    Route::get('/equipements', App\Http\Livewire\Config\Display\Equips::class)->name('config-equips');
    Route::get('/all-sites', App\Http\Livewire\Config\Display\AllSites::class)->name('config-all-sites');
    /***************/

    /*** Add **************/

    // Choose Type Of Host
    Route::view('/add-host', 'config.add-host');

    // Get the Host Info
    Route::get('/add-host/windows', App\Http\Livewire\Config\Add\Host\Windows::class);
    Route::get('/add-host/linux', App\Http\Livewire\Config\Add\Host\Linux::class);
    Route::get('/add-host/router', App\Http\Livewire\Config\Add\Host\Router::class);
    Route::get('/add-host/switch', App\Http\Livewire\Config\Add\Host\Switchs::class);
    Route::get('/add-host/printer', App\Http\Livewire\Config\Add\Host\Printer::class);

    // Create the Host
    Route::get('/add-host/windows/add', [App\Http\Controllers\Config\Add\Host\Windows::class, 'windows'])->name('create-windows-host');
    Route::get('/add-host/linux/add', [App\Http\Controllers\Config\Add\Host\Linux::class, 'linux'])->name('create-linux-host');
    Route::get('/add-host/router/add', [App\Http\Controllers\Config\Add\Host\Router::class, 'router'])->name('create-router-host');
    Route::get('/add-host/switch/add', [App\Http\Controllers\Config\Add\Host\Switchs::class, 'switchs'])->name('create-switch-host');
    Route::get('/add-host/printer/add', [App\Http\Controllers\Config\Add\Host\Printer::class, 'printer'])->name('create-printer-host');
    //----------------------------------------------------------------------------------------------------------------------------------------//

    // Add Service
    Route::get('/add-service', App\Http\Livewire\Config\Add\Services\Service::class);
    Route::get('/create-service', [App\Http\Controllers\Config\Add\Services\Service::class, 'createService'])->name('create-service');

    // Choose Type Of Box
    Route::view('/add-box', 'config.add-box');

    // Add Box
    Route::get('/add-box-type-bf-1010', App\Http\Livewire\Config\Add\Boxes\BF1010::class);
    Route::get('/add-box-type-bf-2300', App\Http\Livewire\Config\Add\Boxes\BF2300::class);

    // Create Box
    Route::get('/add-box-type-bf-1010/create', [App\Http\Controllers\Config\Add\Boxes\BF1010::class, 'createBox'])->name('create-box-BF1010');
    Route::get('/add-box-type-bf-2300/create', [App\Http\Controllers\Config\Add\Boxes\BF2300::class, 'createBox'])->name('create-box-BF2300');

    // Add Pin
    Route::get('/select-box', App\Http\Livewire\Config\Add\Boxes\SelectBox::class);
    Route::get('/add-pin/{id}', App\Http\Livewire\Config\Add\Pins\Pin::class)->name('add-pin');
    Route::get('/create-pin/{id}', [App\Http\Controllers\Config\Add\Pins\Pin::class, 'createPin'])->name('create-pin');

    // Add Equip
    Route::get('/add-equip', App\Http\Livewire\Config\Add\Equip\Equip::class);
    Route::get('/create-equip', [App\Http\Controllers\Config\Add\Equips\Equip::class, 'createEquip'])->name('create-equip');

    /*****************************************************/

    /*** Edit ********************************************/
    // Host :
    Route::get('/edit/host/{id}', App\Http\Livewire\Config\Edit\Host::class)->name('edit-host');
    Route::get('/edited-host/{id}', [App\Http\Controllers\Config\Edit\Host::class, 'editHost'])->name('save-host-edits');

    // Box :
    Route::get('/edit/box/{id}', App\Http\Livewire\Config\Edit\Box::class)->name('edit-box');
    Route::get('/edited-box/{id}', [App\Http\Controllers\Config\Edit\Box::class, 'editBox'])->name('save-box-edits');

    // Service :
    Route::get('/edit/service/{id}', App\Http\Livewire\Config\Edit\Service::class)->name('edit-service');
    Route::get('/edited-service/{id}', [App\Http\Controllers\Config\Edit\Service::class, 'editService'])->name('save-service-edits');

    // Pin :
    Route::get('/edit/pin/{id}', App\Http\Livewire\Config\Edit\Pin::class)->name('edit-pin');
    Route::get('/edited-pin/{id}', [App\Http\Controllers\Config\Edit\Pin::class, 'editPin'])->name('save-pin-edits');

    // Equip :
    Route::get('/edit/equip/{id}', App\Http\Livewire\Config\Edit\Equip::class)->name('edit-equip');
    Route::get('/edited-equip/{id}', [App\Http\Controllers\Config\Edit\Equip::class, 'editEquip'])->name('save-equip-edits');

    // Site :
    // Route::get('/edit/site/{id}',App\Http\Livewire\Config\Edit\Site::class)->name('edit-site');
    Route::get('/edit-site/{id}', [App\Http\Controllers\Config\Edit\Site::class, 'editSite'])->name('edit-site');

    /*****************************************************/

    /*** Delete *******************************************/
    Route::get('/delete-host/{id}', [App\Http\Controllers\Config\Delete\Host::class, 'deleteHost'])->name('delete-host');
    Route::get('/delete-box/{id}', [App\Http\Controllers\Config\Delete\Box::class, 'deleteBox'])->name('delete-box');
    Route::get('/delete-service/{id}', [App\Http\Controllers\Config\Delete\Service::class, 'deleteService'])->name('delete-service');
    Route::get('/delete-pin/{id}', [App\Http\Controllers\Config\Delete\Pin::class, 'deletePin'])->name('delete-pin');
    Route::get('/delete-equip/{id}', [App\Http\Controllers\Config\Delete\Equip::class, 'deleteEquip'])->name('delete-equip');
    Route::get('/delete-site/{id}', [App\Http\Controllers\Config\Delete\Site::class, 'deleteSite'])->name('delete-site');
    /******************************************************/

    /*********************************************** Set Environment ***********************************************************/

    Route::view('/upload-environment', 'upload-environment')->name('upload-environment');
    Route::post('/upload-environment/import', [App\Http\Controllers\Import\Environment::class, 'import'])->name('import-envir');
    Route::view('/display-environment', 'config/set-envirenment')->name('display-environment');
    Route::post('/upload-environment/import/set-environment', [App\Http\Controllers\Config\Import\SetEnvironment::class, 'setEnvironment'])->name('set-environment');

    /***************************************************************************************************************************/
});

/******************************************************************************************************************/

/********************************** Historic **********************************************************************/

Route::group(['prefix' => '/historiques', 'namespace' => 'App\Http\Livewire\Historic\\', 'middleware' => 'auth'], function () {

    Route::get('/hosts', Hosts::class)->name('historic.hosts');
    Route::get('/services', Services::class)->name('historic.services');
    Route::get('/boxes', Boxes::class)->name('historic.boxes');
    Route::get('/equipements', Equips::class)->name('historic.equips');
});

/******************************************************************************************************************/

/********************************** Statistics **********************************************************************/

Route::group(['prefix' => '/statistiques', 'namespace' => 'App\Http\Livewire\Statistic\\', 'middleware' => 'auth'], function () {

    Route::get('/hosts', Hosts::class)->name('statistic.hosts');
    Route::get('/services', Services::class)->name('statistic.services');
    Route::get('/boxes', Boxes::class)->name('statistic.boxes');
    Route::get('/equipements', Equips::class)->name('statistic.equips');
});

/******* Download  *******/
/** PDF */
Route::post('/hosts/pdf', [App\Http\Controllers\Download\PDF\Hosts::class, 'pdf'])->name('hosts.pdf');
Route::post('/services/pdf', [App\Http\Controllers\Download\PDF\Services::class, 'pdf'])->name('services.pdf');
Route::post('/boxes/pdf', [App\Http\Controllers\Download\PDF\Boxes::class, 'pdf'])->name('boxes.pdf');
Route::post('/equipements/pdf', [App\Http\Controllers\Download\PDF\Equips::class, 'pdf'])->name('equips.pdf');
/** CSV */
Route::post('/hosts/csv', [App\Http\Controllers\Download\Excel\Hosts::class, 'csv'])->name('hosts.csv');
Route::post('/services/csv', [App\Http\Controllers\Download\Excel\Services::class, 'csv'])->name('services.csv');
Route::post('/boxes/csv', [App\Http\Controllers\Download\Excel\Boxes::class, 'csv'])->name('boxes.csv');
Route::post('/equipements/csv', [App\Http\Controllers\Download\Excel\Equips::class, 'csv'])->name('equips.csv');

/******************************************************************************************************************/

/********************************** Carte *************************************************************************/

Route::get('/network-map', App\Http\Livewire\NetworkMap::class)->middleware('auth');

/******************************************************************************************************************/

/********************************** Notifocations *****************************************************************/

Route::get('/notifications', App\Http\Livewire\Notifications\Notifications::class)->name('notifications')->middleware('auth');

/******************************************************************************************************************/

/*********************************** Authentication ****************************************************************/

// Registration
Route::get('/register', App\Http\Livewire\Auth\Registration::class)->name('register')->middleware('super_admin');

// Login
Route::get('/login', App\Http\Livewire\Auth\Login::class)->name('login')->middleware('guest');
Route::get('/', App\Http\Livewire\Auth\Login::class)->name('login')->middleware('guest');

// Logout
Route::post('/logout', [App\Http\Controllers\Auth\Logout::class, 'logout'])->name('logout');

/******************************************************************************************************************/

/************************************** User **********************************************************************/

Route::get('/profile', App\Http\Livewire\Auth\User\Profile::class)->name('profile')->middleware('auth');

Route::get('/edit-info', App\Http\Livewire\Auth\User\EditUserInfo::class)->name('edit-user-info')->middleware('auth');

Route::get('/change-password', App\Http\Livewire\Auth\User\ChangePassword::class)->name('change-password')->middleware('auth');

/******************************************************************************************************************/

/************************************** Sites **********************************************************************/

Route::get('/sites', App\Http\Livewire\AllSites::class)->name('sites')->middleware('check_role');
Route::get('/addSite', [App\Http\Controllers\Config\AddSite::class, 'addSite'])->name('new-site');

/*******************************************************************************************************************/
