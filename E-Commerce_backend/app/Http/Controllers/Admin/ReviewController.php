<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:products.view', ['only' => ['index']]);
        $this->middleware('permission:products.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $query = Review::with(['user', 'product'])->latest();

        if ($request->filled('rating')) {
            $query->where('rating', $request->integer('rating'));
        }

        if ($request->filled('product')) {
            $query->whereHas('product', fn ($q) => $q->where('name', 'like', '%'.$request->input('product').'%'));
        }

        return view('admin.reviews.index', [
            'reviews' => $query->paginate(10),
        ]);
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('status', 'Review has been removed successfully.');
    }
}
