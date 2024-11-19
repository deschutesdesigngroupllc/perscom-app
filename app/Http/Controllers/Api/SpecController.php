<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SpecController extends Controller
{
    public function index(): Response
    {
        if (! Storage::disk('local')->exists('specs/specs.json')) {
            throw new NotFoundHttpException('We could not locate the API schema.');
        }

        return response(Storage::disk('local')->get('specs/specs.json'), 200, [
            'Content-Type' => 'application/json',
        ])->withHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET',
        ]);
    }
}
