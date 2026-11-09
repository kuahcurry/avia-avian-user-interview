<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SparePart;
use Illuminate\Http\Request;

class SparePartController extends Controller
{
    public function fetchSpareParts(Request $request)
    {
        $validated = $request->validate([
            'search'    => 'nullable|string',
            'category'  => 'nullable|string',
            'status'    => 'nullable|in:safe,critical',
        ]);

        $query = SparePart::query();

        if($request->filled('search')) {
            $search = $request->search;

            // Bungkus closure agar grouping OR LIKE rapi dalam kurung dan gak tabrakan sama filter lain
            $query->where(function ($q) use ($search) {
                    $q->where('part_name', 'like', '%' . $search . '%')
                    ->orWhere('part_code', 'like', '%' . $search . '%');
            });
        }

        if($request->filled('category')) {
            $category = $request->category;

            $query->where(function ($q) use ($category) {
                    $q->where('category', 'like', '%' . $category . '%');
            });
        }

        if($request->filled('status')) {
            $status = $request->status;

            // Gunakan whereColumn karena status bukan kolom fisik, melainkan perbandingan 2 kolom
            if ($status === 'critical') {
                $query->whereColumn('current_stock', '<=', 'minimum_stock');
            } elseif ($status === 'safe') {
                $query->whereColumn('current_stock', '>', 'minimum_stock');
            }
        }

        $sparePart = $query->get();

        return response()->json([
            'message' => 'Berhasil mengambil data suku cadang | Succesfully loaded spare parts data.',
            'data' => $sparePart,
        ], 200);
    }
}