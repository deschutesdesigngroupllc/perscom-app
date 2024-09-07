<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class SpecController extends Controller
{
    public function index(): Response
    {
        return response(Storage::disk('local')->get('specs/specs.yaml'), 200, [
            'Content-Type' => 'text/yaml',
        ]);
    }
}
