<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use App\Notifications\NewPromotionNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:promotions.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:promotions.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:promotions.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:promotions.delete', ['only' => ['destroy']]);
    }

    public function index(): View
    {
        $promotions = Promotion::withCount('products')->latest()->paginate(10);

        return view('admin.promotions.index', compact('promotions'));
    }

    public function create(): View
    {
        return view('admin.promotions.create', [
            'promotion' => new Promotion(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['nullable', 'boolean'],
            'products' => ['nullable', 'array'],
            'products.*' => ['exists:products,id'],
        ]);

        $promotion = Promotion::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $request->boolean('status'),
        ]);

        if (! empty($validated['products'])) {
            $promotion->products()->sync($validated['products']);
        }

        $promotion->load('products');

        User::where('role', 'customer')->get()->each->notify(new NewPromotionNotification($promotion));

        return redirect()->route('admin.promotions.index')->with('status', 'Promotion created successfully.');
    }

    public function show(Promotion $promotion): View
    {
        $promotion->load('products');

        return view('admin.promotions.show', compact('promotion'));
    }

    public function edit(Promotion $promotion): View
    {
        $promotion->load('products');

        return view('admin.promotions.edit', [
            'promotion' => $promotion,
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['nullable', 'boolean'],
            'products' => ['nullable', 'array'],
            'products.*' => ['exists:products,id'],
        ]);

        $promotion->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $request->boolean('status'),
        ]);

        $promotion->products()->sync($validated['products'] ?? []);

        return redirect()->route('admin.promotions.index')->with('status', 'Promotion updated successfully.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $promotion->delete();

        return redirect()->route('admin.promotions.index')->with('status', 'Promotion deleted successfully.');
    }
}
