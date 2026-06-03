<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\MarketDemand;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class MarketDemandController extends Controller
{
    public function heatmap(): View
    {
        return view('seller.heatmap');
    }

    public function index(): JsonResponse
    {
        $demands = MarketDemand::query()
            ->select('latitude as lat', 'longitude as lng')
            ->get();

        return response()->json($demands);
    }
}
