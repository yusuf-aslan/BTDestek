<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class AssetQueryPrintController extends Controller
{
    public function show(Request $request)
    {
        $anabirim = $request->query('anabirim');
        $model = $request->query('model', 'all');

        // Build query
        $query = Asset::query()->with(['location']);

        if (!empty($anabirim)) {
            $query->whereHas('location', function (Builder $q) use ($anabirim) {
                $q->where('anabirim', $anabirim);
            });
        }

        if (!empty($model) && $model !== 'all') {
            $query->where('model', $model);
        }

        $assets = $query->orderBy('name', 'asc')->get();

        return view('admin.asset-query-print', [
            'assets' => $assets,
            'anabirim' => $anabirim,
            'model' => $model,
            'printDate' => now()->format('d.m.Y H:i'),
        ]);
    }
}
