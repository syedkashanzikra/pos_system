<?php 

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductLedger;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductLedgerExport;

class ProductLedgerController extends Controller
{
    public function index($productId)
    {
        $product = Product::with('ledgers')->findOrFail($productId);
        return view('product-ledger.index', compact('product'));
    }

    
    public function downloadExcel($productId)
    {
        return Excel::download(new ProductLedgerExport($productId), 'product-ledger-'.$productId.'.xlsx');
    }
    // public function store(Request $request, $productId)
    // {
    //     $product = Product::findOrFail($productId);

    //     $in = $request->type === 'add' ? $request->quantity : 0;
    //     $out = $request->type === 'sub' ? $request->quantity : 0;

    //     $lastBalance = $product->ledgers()->latest()->first()?->balance ?? 0;
    //     $newBalance = $lastBalance + $in - $out;

    //     $ledger = ProductLedger::create([
    //         'product_id' => $product->id,
    //         'type' => 'adjustment',
    //         'reference' => $request->reference,
    //         'date' => now(),
    //         'quantity_in' => $in,
    //         'quantity_out' => $out,
    //         'balance' => $newBalance,
    //     ]);

    //     return redirect()->back()->with('success', 'Ledger updated');
    // }
}
