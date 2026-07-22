<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewProductNotification;
use App\Services\ProductAlertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:products.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:products.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:products.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:products.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $query = Product::with('category');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $status = filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($status !== null) {
                $query->where('status', $status);
            }
        }

        return view('admin.products.index', [
            'products' => $query->latest()->paginate(10),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function show(Product $product): View
    {
        $product->load('category', 'reviews.user', 'activePromotions');

        return view('admin.products.show', compact('product'));
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $existing = Product::withTrashed()->where('name', $request->input('name'))->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }

            $data = $this->validateProduct($request, $existing->id);
            $image = $this->storeImage($request);

            $data['stock'] = $existing->stock + $data['stock'];
            $data['slug'] = $existing->slug;

            if ($image) {
                $this->deleteImage($existing->image);
                $data['image'] = $image;
            }

            $existing->update($data);

            app(ProductAlertService::class)->checkLowStock($existing->fresh());
            app(ProductAlertService::class)->checkOutOfStock($existing->fresh());

            User::where('role', 'customer')->get()->each->notify(new NewProductNotification($existing, 'updated'));

            return redirect()->route('admin.products.index')->with('status', "<strong>{$existing->name}</strong> already exists — stock increased by {$request->input('stock')} to {$existing->fresh()->stock}.");
        }

        $data = $this->validateProduct($request);
        $data['image'] = $this->storeImage($request);

        $product = Product::create($data);

        User::where('role', 'customer')->get()->each->notify(new NewProductNotification($product, 'created'));

        return redirect()->route('admin.products.index')->with('status', "<strong>{$product->name}</strong> has been created successfully.");
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validateProduct($request, $product->id);
        $image = $this->storeImage($request);

        if ($image) {
            $this->deleteImage($product->image);
            $data['image'] = $image;
        }

        $product->update($data);

        User::where('role', 'customer')->get()->each->notify(new NewProductNotification($product, 'updated'));

        app(ProductAlertService::class)->checkLowStock($product->fresh());
        app(ProductAlertService::class)->checkOutOfStock($product->fresh());

        return redirect()->route('admin.products.index')->with('status', "<strong>{$product->name}</strong> has been updated successfully.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteImage($product->image);
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', "<strong>{$product->name}</strong> has been archived.");
    }

    private function validateProduct(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:4096'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        $slug = Str::slug($validated['name']);
        $counter = 1;
        $query = Product::query()->where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $slug = Str::slug($validated['name']).'-'.(++$counter);
            $query = Product::query()->where('slug', $slug);

            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return [
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'status' => $request->boolean('status'),
            'expiry_date' => $validated['expiry_date'] ?? null,
        ];
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('products', 'public');
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
