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

Route::get('/overview', App\Http\Livewire\Overview::class)->name('overview')->middleware('auth');

/********************************** Monitoring *********************************************************************/

Route::get('/monitoring/hosts', App\Http\Livewire\Monitoring\Hosts::class)->name('monitoring.hosts')->middleware('auth');
Route::get('/monitoring/hosts/{id}', App\Http\Livewire\Monitoring\Details\Host::class)->name('mh-details')->middleware('auth');

Route::get('/monitoring/services', App\Http\Livewire\Monitoring\Services::class)->name('monitoring.services')->middleware('auth');
Route::get('/monitoring/services/{id}', App\Http\Livewire\Monitoring\Details\Service::class)->name('ms-details')->middleware('auth');

Route::get('/monitoring/boxes', App\Http\Livewire\Monitoring\Boxes::class)->name('monitoring.boxes')->middleware('auth');
Route::get('/monitoring/boxes/{id}', App\Http\Livewire\Monitoring\Details\Box::class)->name('mb-details')->middleware('auth');

Route::get('/monitoring/equipements', App\Http\Livewire\Monitoring\Equips::class)->name('monitoring.equips')->middleware('auth');
Route::get('/monitoring/equipements/{id}', App\Http\Livewire\Monitoring\Details\Equip::class)->name('me-details')->middleware('auth');

/******************************************************************************************************************/

/********************************** Problems **********************************************************************/

Route::get('/problems/hosts', App\Http\Livewire\Problems\Hosts::class)->name('problems.hosts')->middleware('auth');
Route::get('/problems/services', App\Http\Livewire\Problems\Services::class)->name('problems.services')->middleware('auth');
Route::get('/problems/boxes', App\Http\Livewire\Problems\Boxes::class)->name('problems.boxes')->middleware('auth');
Route::get('/problems/equipements', App\Http\Livewire\Problems\Equips::class)->name('problems.equips')->middleware('auth');

/******************************************************************************************************************/

/*********************************** Groups ***********************************************************************/

Route::get('/config/hostgroups',App\Http\Livewire\Groups\Hosts::class)->middleware(['auth','agent']);
Route::get('/config/hostgroup',App\Http\Livewire\Groups\Details\Hostgroup::class)->name('hg-details')->middleware(['auth','agent']);

Route::get('/config/servicegroups',App\Http\Livewire\Groups\Services::class)->middleware(['auth','agent']);
Route::get('/config/servicegroups/{id}',App\Http\Livewire\Groups\Details\Servicegroup::class)->name('sg-details')->middleware(['auth','agent']);

Route::get('/config/boxgroups',App\Http\Livewire\Groups\Boxes::class)->middleware(['auth','agent']);
Route::get('/config/boxgroups/{id}',App\Http\Livewire\Groups\Details\Boxgroup::class)->name('bg-details')->middleware(['auth','agent']);

Route::get('/config/equipgroups',App\Http\Livewire\Groups\Equips::class)->middleware(['auth','agent']);
Route::get('/config/equipgroups/{id}',App\Http\Livewire\Groups\Details\Equipgroup::class)->name('eg-details')->middleware(['auth','agent']);

//------ Config ------------------------------------/

// Add HG
Route::get('/config/add-hostgroup',[App\Http\Controllers\Config\Add\Groups\Hostgroup::class, 'addHG'])->name('addHG');
Route::get('/config/add-hostgroup/create',[App\Http\Controllers\Config\Add\Groups\Hostgroup::class, 'createHG'])->name('createHG');

// Add SG
Route::get('/config/add-servicegroup',[App\Http\Controllers\Config\Add\Groups\Servicegroup::class, 'addSG'])->name('addSG');
Route::get('/config/add-servicegroup/create',[App\Http\Controllers\Config\Add\Groups\Servicegroup::class, 'createSG'])->name('createSG');

// Add BG
Route::get('/config/add-boxgroup',[App\Http\Controllers\Config\Add\Groups\Boxgroup::class, 'addBG'])->name('addBG');
Route::get('/config/add-boxgroup/create',[App\Http\Controllers\Config\Add\Groups\Boxgroup::class, 'createBG'])->name('createBG');

// Add EG
Route::get('/config/add-equipgroup',[App\Http\Controllers\Config\Add\Groups\Equipgroup::class, 'addEG'])->name('addEG');
Route::get('/config/add-equipgroup/create',[App\Http\Controllers\Config\Add\Groups\Equipgroup::class, 'createEG'])->name('createEG');

// Edit HG
Route::get('/config/manage-hostgroup/{id}',[App\Http\Controllers\Config\Groups\ManageHG::class, 'manageHG'])->name('manageHG');
Route::get('/config/edit-hostgroup/{id}',[App\Http\Controllers\Config\Edit\Groups\Hostgroup::class, 'editHG'])->name('editHG');

// Delete HG
Route::get('/config/delete-hostgroup/{id}',[App\Http\Controllers\Config\Delete\Groups\Hostgroup::class, 'deleteHG'])->name('deleteHG');
//-----------------------------------------------------------------------------------------------------------------------------------//

// Edit BG
Route::get('/config/manage-boxgroup/{id}',[App\Http\Controllers\Config\Groups\ManageBG::class, 'manageBG'])->name('manageBG');
Route::get('/config/edit-boxgroup/{id}',[App\Http\Controllers\Config\Edit\Groups\Boxgroup::class, 'editBG'])->name('editBG');

// Delete BG
Route::get('/config/delete-boxgroup/{id}',[App\Http\Controllers\Config\Delete\Groups\Boxgroup::class, 'deleteBG'])->name('deleteBG');
//-----------------------------------------------------------------------------------------------------------------------------------//

// Edit SG
Route::get('/config/manage-servicegroup/{id}',[App\Http\Controllers\Config\Groups\ManageSG::class, 'manageSG'])->name('manageSG');
Route::get('/config/edit-servicegroup/{id}',[App\Http\Controllers\Config\Edit\Groups\Servicegroup::class, 'editSG'])->name('editSG');

// Delete SG
Route::get('/config/delete-servicegroup/{id}',[App\Http\Controllers\Config\Delete\Groups\Servicegroup::class, 'deleteSG'])->name('deleteSG');
//-----------------------------------------------------------------------------------------------------------------------------------//

// Edit EG
Route::get('/config/manage-equipgroup/{id}',[App\Http\Controllers\Config\Groups\ManageEG::class, 'manageEG'])->name('manageEG');
Route::get('/config/edit-equipgroup/{id}',[App\Http\Controllers\Config\Edit\Groups\Equipgroup::class, 'editEG'])->name('editEG');

// Delete EG
Route::get('/config/delete-equipgroup/{id}',[App\Http\Controllers\Config\Delete\Groups\Equipgroup::class, 'deleteEG'])->name('deleteEG');

//------------------------------------------------/

/******************************************************************************************************************/

/************************************ Config **********************************************************************/

Route::get('/config/users',App\Http\Livewire\Auth\Config\Users::class)->name('config.users')->middleware(['auth','agent']);

/*** Display ***/
Route::get('/config/hosts',App\Http\Livewire\Config\Display\Hosts::class)->name('config-hosts')->middleware(['auth','agent']);
Route::get('/config/boxes',App\Http\Livewire\Config\Display\Boxes::class)->name('config-boxes')->middleware(['auth','agent']);
Route::get('/config/services',App\Http\Livewire\Config\Display\Services::class)->name('config-services')->middleware(['auth','agent']);
Route::get('/config/equipements',App\Http\Livewire\Config\Display\Equips::class)->name('config-equips')->middleware(['auth','agent']);
/***************/

/*** Add *******************************************/

// Choose Type Of Host
Route::view('/config/add-host','config.add-host')->middleware(['auth','agent']);

// Get the Host Info
Route::get('/config/add-host/windows', App\Http\Livewire\Config\Add\Host\Windows::class)->middleware(['auth','agent']);
Route::get('/config/add-host/linux', App\Http\Livewire\Config\Add\Host\Linux::class)->middleware(['auth','agent']);
Route::get('/config/add-host/router', App\Http\Livewire\Config\Add\Host\Router::class)->middleware(['auth','agent']);
Route::get('/config/add-host/switch', App\Http\Livewire\Config\Add\Host\Switchs::class)->middleware(['auth','agent']);
Route::get('/config/add-host/printer', App\Http\Livewire\Config\Add\Host\Printer::class)->middleware(['auth','agent']);

// Create the Host
Route::get('/config/add-host/windows/add', [App\Http\Controllers\Config\Add\Host\Windows::class,'windows'])->name('create-windows-host');
Route::get('/config/add-host/linux/add', [App\Http\Controllers\Config\Add\Host\Linux::class,'linux'])->name('create-linux-host');
Route::get('/config/add-host/router/add', [App\Http\Controllers\Config\Add\Host\Router::class,'router'])->name('create-router-host');
Route::get('/config/add-host/switch/add', [App\Http\Controllers\Config\Add\Host\Switchs::class,'switchs'])->name('create-switch-host');
Route::get('/config/add-host/printer/add', [App\Http\Controllers\Config\Add\Host\Printer::class,'printer'])->name('create-printer-host');
//----------------------------------------------------------------------------------------------------------------------------------------//

// Add Service
Route::get('/config/add-service', App\Http\Livewire\Config\Add\Services\Service::class)->middleware('agent');
Route::get('/config/create-service', [App\Http\Controllers\Config\Add\Services\Service::class,'createService'])->name('create-service');

// Add Box
Route::get('/config/add-box',App\Http\Livewire\Config\Add\Boxes\Box::class);
// Create Box
Route::get('/config/add-box/create', [App\Http\Controllers\Config\Add\Boxes\Box::class,'createBox'])->name('create-box');

// Add Equipement
Route::get('/config/select-box', App\Http\Livewire\Config\Add\Boxes\SelectBox::class)->middleware('agent');
Route::get('/config/add-equipement/{id}', App\Http\Livewire\Config\Add\Equips\Equip::class)->name('add-equip')->middleware('agent');
Route::get('/config/create-equipement/{id}', [App\Http\Controllers\Config\Add\Equips\Equip::class,'createEquip'])->name('create-equip');

/*****************************************************/

/*** Edit ********************************************/
// Host :
Route::get('/config/edit/host/{id}',App\Http\Livewire\Config\Edit\Host::class)->name('edit-host')->middleware('agent');
Route::get('/config/edited-host/{id}',[App\Http\Controllers\Config\Edit\Host::class, 'editHost'])->name('save-host-edits');

// Box :
Route::get('/config/edit/box/{id}',App\Http\Livewire\Config\Edit\Box::class)->name('edit-box')->middleware('agent');
Route::get('/config/edited-box/{id}',[App\Http\Controllers\Config\Edit\Box::class, 'editBox'])->name('save-box-edits');

// Service :
Route::get('/config/edit/service/{id}',App\Http\Livewire\Config\Edit\Service::class)->name('edit-service')->middleware('agent');
Route::get('/config/edited-service/{id}',[App\Http\Controllers\Config\Edit\Service::class, 'editService'])->name('save-service-edits');

// Equip :
Route::get('/config/edit/equip/{id}',App\Http\Livewire\Config\Edit\Equip::class)->name('edit-equip')->middleware('agent');
Route::get('/config/edited-equip/{id}',[App\Http\Controllers\Config\Edit\Equip::class, 'editEquip'])->name('save-equip-edits');
/*****************************************************/

/*** Delete *******************************************/
Route::get('/config/delete-host/{id}',[App\Http\Controllers\Config\Delete\Host::class, 'deleteHost'])->name('delete-host');
Route::get('/config/delete-box/{id}',[App\Http\Controllers\Config\Delete\Box::class, 'deleteBox'])->name('delete-box');
Route::get('/config/delete-service/{id}',[App\Http\Controllers\Config\Delete\Service::class, 'deleteService'])->name('delete-service');
Route::get('/config/delete-equip/{id}',[App\Http\Controllers\Config\Delete\Equip::class, 'deleteEquip'])->name('delete-equip');
/******************************************************/

/******************************************************************************************************************/

/********************************** Historic **********************************************************************/

Route::get('/historiques/hosts', App\Http\Livewire\Historic\Hosts::class)->name('historic.hosts')->middleware('auth');
Route::get('/historiques/services', App\Http\Livewire\Historic\Services::class)->name('historic.services')->middleware('auth');
Route::get('/historiques/boxes', App\Http\Livewire\Historic\Boxes::class)->name('historic.boxes')->middleware('auth');
Route::get('/historiques/equipements', App\Http\Livewire\Historic\Equips::class)->name('historic.equips')->middleware('auth');

/******************************************************************************************************************/

/********************************** Historic **********************************************************************/

Route::get('/statistiques/hosts', App\Http\Livewire\Statistic\Hosts::class)->name('statistic.hosts')->middleware('auth');
Route::get('/statistiques/services', App\Http\Livewire\Statistic\Services::class)->name('statistic.services')->middleware('auth');
Route::get('/statistiques/boxes', App\Http\Livewire\Statistic\Boxes::class)->name('statistic.boxes')->middleware('auth');
Route::get('/statistiques/equipements', App\Http\Livewire\Statistic\Equips::class)->name('statistic.equips')->middleware('auth');

/******* Download  *******/
/** PDF */
Route::get('/hosts/pdf',[App\Http\Controllers\Download\PDF\Hosts::class,'pdf'])->name('hosts.pdf');
Route::get('/services/pdf',[App\Http\Controllers\Download\PDF\Services::class,'pdf'])->name('services.pdf');
Route::get('/boxes/pdf',[App\Http\Controllers\Download\PDF\Boxes::class,'pdf'])->name('boxes.pdf');
Route::get('/equipements/pdf',[App\Http\Controllers\Download\PDF\Equips::class,'pdf'])->name('equips.pdf');
/** CSV */
Route::get('/hosts/csv',[App\Http\Controllers\Download\Excel\Hosts::class,'csv'])->name('hosts.csv');
Route::get('/services/csv',[App\Http\Controllers\Download\Excel\Services::class,'csv'])->name('services.csv');
Route::get('/boxes/csv',[App\Http\Controllers\Download\Excel\Boxes::class,'csv'])->name('boxes.csv');
Route::get('/equipements/csv',[App\Http\Controllers\Download\Excel\Equips::class,'csv'])->name('equips.csv');

/******************************************************************************************************************/

/********************************** Carte *************************************************************************/

Route::get('/network-map', App\Http\Livewire\NetworkMap::class)->middleware('auth');

/******************************************************************************************************************/

/********************************** Notifocations *****************************************************************/

Route::get('/notifications', App\Http\Livewire\Notifications\Notifications::class)->name('notifications')->middleware('auth');

/******************************************************************************************************************/

/*********************************** Authentication ****************************************************************/

// Registration
Route::get('/register', App\Http\Livewire\Auth\Registration::class)->name('register')->middleware('auth');

// Login
Route::get('/login', App\Http\Livewire\Auth\Login::class)->name('login')->middleware('guest');
Route::get('/', App\Http\Livewire\Auth\Login::class)->name('login')->middleware('guest');

// Logout
Route::post('/logout', [App\Http\Controllers\Auth\Logout::class,'logout'])->name('logout')->middleware('auth');

/******************************************************************************************************************/

/************************************** User **********************************************************************/

Route::get('/profile', App\Http\Livewire\Auth\User\Profile::class)->name('profile')->middleware('auth');

Route::get('/edit-info', App\Http\Livewire\Auth\User\EditUserInfo::class)->name('edit-user-info')->middleware('auth');

Route::get('/change-password', App\Http\Livewire\Auth\User\ChangePassword::class)->name('change-password')->middleware('auth');

/******************************************************************************************************************/

// Route::get('',[App\Http\Controllers\test::class,'test'])->name('test');

Route::view('/welcome', 'welcome');
