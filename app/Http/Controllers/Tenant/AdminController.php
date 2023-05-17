<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spark\Http\Controllers\DownloadReceiptController;

class AdminController extends Controller
{
    /**
     * @return mixed
     */
    public function downloadReceipt(Request $request, $id)
    {
        return app()->call(DownloadReceiptController::class.'@__invoke', [
            'request' => $request,
            'type' => 'tenant',
            'id' => tenant()->getTenantKey(),
            'receiptId' => $id,
        ]);
    }
}
