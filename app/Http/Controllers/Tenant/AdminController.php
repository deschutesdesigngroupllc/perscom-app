<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spark\Http\Controllers\DownloadReceiptController;

class AdminController extends Controller
{
    public function downloadReceipt(Request $request, string $id): mixed
    {
        return app()->call(DownloadReceiptController::class.'@__invoke', [
            'request' => $request,
            'type' => 'tenant',
            'id' => tenant()->getTenantKey(),
            'receiptId' => $id,
        ]);
    }
}
