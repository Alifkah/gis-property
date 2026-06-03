<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\PropertyAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['nullable', 'string', 'max:20'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'district_name' => ['nullable', 'string', 'max:100'],
        ]);

        $alert = auth()->user()->alerts()->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi pencarian berhasil diaktifkan!',
            'data' => $alert,
        ]);
    }

    public function destroy(PropertyAlert $alert): JsonResponse
    {
        if ($alert->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $alert->delete();

        return response()->json(['success' => true, 'message' => 'Notifikasi pencarian berhasil dihapus.']);
    }
}
