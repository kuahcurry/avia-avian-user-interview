<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\SparePart;
use App\Models\SparePartUsage;
use App\Models\StockHistory;

class SparePartUsageController extends Controller
{
    public function recordUsage(Request $request)
    {
        $validated = $request->validate([
            'machine_id'    => 'required|exists:machines,id',
            'spare_part_id' => 'required|exists:spare_parts,id',
            'quantity_used' => 'required|integer|min:1',
            'notes'         => 'nullable|string',
            'recorded_by'   => 'required|string',
        ]);

        $sparePart = SparePart::findOrFail($validated['spare_part_id']);

        if ($sparePart->current_stock < $validated['quantity_used'])
        {
            return response()->json([
                'error' => 'Kekurangan stok untuk suku cadang ini. Stok saat ini:'. $sparePart->current_stock
            ], 400);
        };

        $usage = DB::transaction(function () use ($validated, $sparePart)
        {
            $sparePart = SparePart::where('id', $validated['spare_part_id'])
                          ->lockForUpdate()
                          ->firstOrFail();

            $usage = SparePartUsage::create([
                'machine_id'    => $validated['machine_id'],
                'spare_part_id' => $validated['spare_part_id'],
                'quantity_used' => $validated['quantity_used'],
                'notes'         => $validated['notes'] ?? null,
                'recorded_by'   => $validated['recorded_by'],
                'usage_date'    => now()->toDateString(),
            ]);

            $oldStock = $sparePart->current_stock;
            $newStock = $oldStock - $validated['quantity_used'];

            $sparePart->current_stock = $newStock;
            $sparePart->save();

            StockHistory::create([
                'spare_part_id'    => $validated['spare_part_id'],
                'quantity_changed' => -$validated['quantity_used'],
                'old_stock'        => $oldStock,
                'new_stock'        => $newStock,
                'reason'           => 'usage',
                'changed_by'       => $validated['recorded_by'],
            ]);

            return $usage;
        });

        return response()->json ([
            'message'   => 'Spare part usage recorded successfully | Penggunaan suku cadang berhasil dicatat.',
            'data'      => $usage,
        ], 201);
    }
}