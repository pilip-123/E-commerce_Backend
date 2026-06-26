<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use App\Notifications\NewReviewNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Review::with(['user', 'product.category'])->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->integer('product_id'));
        }

        return response()->json([
            'data' => $query->paginate($request->integer('per_page', 10)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $review = Review::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $validated['product_id'],
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        $review->load('user', 'product');

        User::where('role', 'admin')->get()->each->notify(new NewReviewNotification($review));

        return response()->json([
            'message' => 'Review saved successfully.',
            'data' => $review,
        ], 201);
    }
}
