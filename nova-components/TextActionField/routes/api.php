<?php

Route::post('/action', function (\Illuminate\Http\Request $request) {
    if (! $actionCallback = $request->input('actionCallback')) {
        return response()->noContent();
    }

    $closure = unserialize($actionCallback)->getClosure();

    $response = rescue(function () use ($closure) {
        return call_user_func($closure);
    }, response()->noContent());

    return response()->json([
        'value' => $response,
    ]);
});
