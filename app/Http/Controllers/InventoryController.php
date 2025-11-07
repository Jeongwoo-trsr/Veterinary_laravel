<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search', '');
        $category = $request->input('category', '');

        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $query = InventoryItem::query();

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('supplier_name', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if (!empty($category)) {
            $query->where('category', $category);
        }

        // Get all items with pagination
        $items = $query->orderBy('created_at', 'desc')
                       ->paginate(15)
                       ->appends([
                           'search' => $search,
                           'category' => $category
                       ]);

        // Calculate statistics
        $totalItems = InventoryItem::count();
        $lowStockCount = InventoryItem::lowStock()->count();
        $expiredCount = InventoryItem::expired()->count();
        $topUsedItems = InventoryItem::orderBy('current_stock', 'asc')
                                    ->where('current_stock', '>', 0)
                                    ->limit(4)
                                    ->get();

        // Get all categories for filter dropdown
        $categories = ['Medicine', 'Consumables', 'Equipment', 'Pet Food'];

        return view('admin.inventory', compact(
            'items',
            'totalItems',
            'lowStockCount',
            'expiredCount',
            'topUsedItems',
            'search',
            'category',
            'categories'
        ));
    }

    public function create()
    {
        return view('admin.inventory-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Medicine,Consumables,Equipment,Pet Food',
            'description' => 'nullable|string|max:1000',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock_level' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'selling_price' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:100',
            'supplier_name' => 'nullable|string|max:255',
        ]);

        InventoryItem::create($request->all());

        return redirect()->route('inventory.index')->with('success', 'Item added successfully.');
    }

    public function show(InventoryItem $inventory)
    {
        $inventory->load('transactions.user');
         return response()->json($inventory);
    }

    public function edit(InventoryItem $inventory)
    {
        return view('admin.inventory-edit', compact('inventory'));
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Medicine,Consumables,Equipment,Pet Food',
            'description' => 'nullable|string|max:1000',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock_level' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'selling_price' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:100',
            'supplier_name' => 'nullable|string|max:255',
        ]);

        $inventory->update($request->all());

        return redirect()->route('inventory.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(InventoryItem $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventory.index')->with('success', 'Item deleted successfully.');
    }

    public function adjustStock(Request $request, InventoryItem $inventory)
    {
        $request->validate([
            'adjustment_type' => 'required|in:add,reduce',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($request->adjustment_type === 'add') {
            $inventory->addStock($request->quantity, 'manual_adjustment', $request->notes);
        } else {
            $inventory->reduceStock($request->quantity, 'manual_adjustment', $request->notes);
        }

        return redirect()->route('inventory.index')->with('success', 'Stock adjusted successfully.');
    }
}