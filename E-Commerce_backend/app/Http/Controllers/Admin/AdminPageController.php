<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminPageController extends Controller
{
    public function customers(): View
    {
        return view('admin.customers', [
            'users' => User::withCount('orders')
                ->withSum('orders', 'total_amount')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function profile(): View
    {
        return view('admin.profile', [
            'user' => auth()->user(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
            'address' => $validated['address'] ?? $user->address,
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
        ];

        if ($request->hasFile('image')) {
            if ($user->image_url) {
                Storage::disk('public')->delete($user->image_url);
            }
            $data['image_url'] = $request->file('image')->store('users', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.profile')->with('status', 'Profile updated successfully.');
    }
}
