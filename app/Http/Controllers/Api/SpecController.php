<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SpecController extends Controller
{
    public function index(): Response
    {
        $response = Http::get('https://raw.githubusercontent.com/deschutesdesigngroupllc/perscom-docs/master/api-reference/openapi.json');

        if (! $response->successful()) {
            throw new NotFoundHttpException('The requested file could not be found.');
        }

        return response($response->body(), 200, [
            'Content-Type' => 'application/json',
        ])->withHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET',
        ]);
    }
}
