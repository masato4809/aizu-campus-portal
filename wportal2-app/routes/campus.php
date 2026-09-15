<?php

declare(strict_types=1);

use App\Http\Controllers\Campus\AuthController;
use App\Http\Controllers\Campus\CatalogPageController;
use App\Http\Controllers\Campus\ClubController;
use App\Http\Controllers\Campus\DiagnosisController;
use App\Http\Controllers\Campus\PageController;
use App\Http\Controllers\Campus\ProfileController;
use App\Http\Controllers\Campus\ReviewController;
use App\Http\Controllers\Campus\ReviewHelpfulController;
use App\Http\Controllers\Campus\TeacherController;
use App\Http\Middleware\CampusAuthenticate;
use Illuminate\Support\Facades\Route;

Route::prefix('campus')->middleware(\App\Http\Middleware\RequireCampusDiagnosis::class)->group(function () {
    Route::get('/club-images/{image}', [ClubController::class, 'image'])
        ->whereNumber('image')->middleware(CampusAuthenticate::class);
    Route::get('/club-catalog-images/{catalog}', [ClubController::class, 'catalogImage'])
        ->whereNumber('catalog')->middleware(CampusAuthenticate::class);

    Route::get('/students/{student}', [CatalogPageController::class, 'student'])->whereNumber('student')->middleware(CampusAuthenticate::class);

    Route::get('/review/thanks', [ReviewController::class, 'thanks'])->middleware(CampusAuthenticate::class);

    Route::get('/messages', fn () => abort(404));
    Route::post('/message', fn () => abort(404));
    Route::get('/{page?}', [PageController::class, 'index'])
        ->where('page', 'courses|professors|matches|clubs|profile|diagnosis|login|register');

    Route::get('/professors/{teacher}', [CatalogPageController::class, 'professor'])->whereNumber('teacher')->middleware(CampusAuthenticate::class);
    Route::get('/courses/{course}', [CatalogPageController::class, 'course'])->whereNumber('course')->middleware(CampusAuthenticate::class);

    Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])->whereNumber('teacher')->middleware(CampusAuthenticate::class);

    Route::middleware('throttle:30,1')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [ProfileController::class, 'save'])->defaults('action', 'register');
        Route::middleware(CampusAuthenticate::class)->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/profile', [ProfileController::class, 'save'])->defaults('action', 'profile');
            Route::post('/teachers/{teacher}/reviews', [TeacherController::class, 'store'])->whereNumber('teacher');
            Route::post('/reviews/{review}/helpful', [ReviewHelpfulController::class, 'store'])->whereNumber('review');
            Route::post('/review', [ReviewController::class, 'store']);
            Route::post('/club', [ClubController::class, 'store']);
            Route::post('/diagnosis', [DiagnosisController::class, 'store']);
            Route::put('/club-catalog/{catalog}', [ClubController::class, 'updateCatalog'])->whereNumber('catalog');
            Route::delete('/club/{club}', [ClubController::class, 'destroy'])->whereNumber('club');
        });
    });
});
