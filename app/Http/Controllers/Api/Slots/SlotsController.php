<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Slots;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\SlotRequest;
use App\Models\Slot;
use Orion\Http\Controllers\Controller;

class SlotsController extends Controller
{
    use AuthorizesRequests;

    protected $model = Slot::class;

    protected $request = SlotRequest::class;

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return ['assignment_records', 'assignment_records.*', 'position', 'specialty', 'units', 'users', 'users.*'];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'position_id', 'specialty_id', 'description', 'empty', 'order', 'hidden', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'position_id', 'specialty_id', 'description', 'empty', 'order', 'hidden', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'position_id', 'specialty_id', 'description', 'empty', 'order', 'hidden', 'created_at', 'updated_at'];
    }
}
