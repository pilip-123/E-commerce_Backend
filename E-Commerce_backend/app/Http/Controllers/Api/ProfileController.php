<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => $this->userPayload($user),
            'summary' => [
                'orders' => Order::where('user_id', $user->id)->count(),
                'wishlist' => Wishlist::where('user_id', $user->id)->count(),
                'cart' => Cart::where('user_id', $user->id)->count(),
                'reviews' => Review::where('user_id', $user->id)->count(),
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $user = $request->user();

        $data = [
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? $user->phone,
            'address' => $validated['address'] ?? $user->address,
        ];

        if ($request->hasFile('image')) {
            if ($user->image_url) {
                Storage::disk('public')->delete($user->image_url);
            }
            $data['image_url'] = $request->file('image')->store('users', 'public');
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $this->userPayload($user->fresh()),
        ]);
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'phone' => $user->phone,
            'address' => $user->address,
            'image_url' => $user->image_url
                ? rtrim(request()->getSchemeAndHttpHost(), '/').Storage::url($user->image_url)
                : null,
        ];
    }
}
