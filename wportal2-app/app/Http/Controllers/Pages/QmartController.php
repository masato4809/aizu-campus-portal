<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Response;

class QmartController extends Controller
{
    public function invoke(Request $request): Response
    {
        return $this->render('Qmart/Index');
    }
}
