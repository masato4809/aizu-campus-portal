<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages\Sample;

use App\Http\Controllers\Controller;
use Inertia\Response;

class InternController extends Controller
{
    public function invoke(): Response
    {
        return $this->render('Sample/Intern/Index');
    }
}
