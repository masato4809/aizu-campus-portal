<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages\Sample;

use App\Http\Controllers\Controller;
use Inertia\Response;

class RedisController extends Controller
{
    public function invoke(): Response
    {
        return $this->render('Sample/Redis/Index');
    }
}
