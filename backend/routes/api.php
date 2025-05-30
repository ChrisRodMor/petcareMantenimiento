<?php

use App\Http\Controllers\AbuseReportController;
use App\Http\Controllers\AdoptionReportController;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LostPetReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VaccineController;
use App\Http\Controllers\AnimalDeletionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Autenticación
Route::post('/register', [ClientController::class, 'store']);
Route::post('/login', [AuthController::class, 'authenticate']);

//      publicas
//Especies
Route::get('/types', [TypeController::class, 'index']);

//Razas
Route::get('/breeds/{type}', [TypeController::class, 'getBreeds']);

//Animals
Route::get('/animals',[AnimalController::class, 'index']);
Route::post('/search-animals', [AnimalController::class, 'searchAnimals']);
Route::get('/animals/{animal}', [AnimalController::class, 'show']);

//Vacunas
Route::get('/vaccines/{animal}', [VaccineController::class, 'getVaccines']);
Route::post('/store-vaccine/{idAnimal}', [VaccineController::class, 'store']);

//Grupo de rutas con el middleware de autenticación con Sanctum (si no está logueado el usuario, no puede acceder)
Route::middleware(['auth:sanctum'])->group(function () {
    //cuenta
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::get('/profile', [UserController::class, 'profile']);

    //clientes
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/clients/{client}', [ClientController::class, 'show']);
    Route::post('/clients-search',[ClientController::class,'search']);

    //animales
    Route::post('/store-animals', [AnimalController::class, 'store']);
    Route::post('/update-animals/{animal}', [AnimalController::class, 'update']);
    Route::post('/delete-animals/{id}', [AnimalDeletionController::class, 'store']);

    //reportes
    
    Route::get('/reports', [ReportController::class, 'index']);// Listar todos los reportes
    Route::get('/user-reports', [ReportController::class, 'getReports']); // Listar reportes del usuario autenticado
    Route::get('/report/{report}', [ReportController::class, 'show']);   // Ver un reporte específico
    Route::get('/lost-pets',[LostPetReportController::class,'index']); // Listar reportes de mascotas perdidas

    Route::post('/store-abuse-report',[AbuseReportController::class,'store']); // Registrar un reporte de abuso
    Route::post('/store-adoption-report',[AdoptionReportController::class,'store']); // Registrar un reporte de adopción
    Route::post('/store-lost-pet',[LostPetReportController::class,'store']); // Registrar un reporte de mascota perdida

    Route::post('/report-update-status/{report}',[ReportController::class, 'updateStatus']); // Actualizar el estado de un reporte
    Route::post('/lost-pet-status/{idReport}',[LostPetReportController::class,'updateStatus']);

    //dashboard
    // 1. Conteos de animales en el refugio
    Route::get('/dashboard/animals/counts',[AnimalController::class, 'animalsCounts']);
    // 2. Cantidad de bajas por motivo
    Route::get('/dashboard/animals/deletions',[AnimalDeletionController::class, 'summary']);
    // 3. Totales de ingresos (mensual y anual)
    Route::get('/dashboard/animals/income',[AnimalController::class, 'incomeSummary']);
    // 4. Cantidad de adopciones por mes y año
    Route::get('/dashboard/adoptions',[AdoptionReportController::class, 'adoptionsSummary']);
    // 5. Cantidad de reportes atendidos y pendientes
    Route::get('/dashboard/reports',[ReportController::class, 'statusSummary']);
    // 6. Tasa de recuperación de mascotas perdidas
    Route::get('/dashboard/lost-pets',[LostPetReportController::class, 'lostPetsRecovery']);

});
