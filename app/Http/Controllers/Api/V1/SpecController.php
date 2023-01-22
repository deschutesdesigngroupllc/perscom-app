<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class SpecController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        return response(Storage::disk('local')->get('specs/specs.yaml'), 200, [
            'Content-Type' => 'text/yaml',
        ]);
    }
}
