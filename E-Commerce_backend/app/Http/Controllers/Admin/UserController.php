<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:users.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:users.edit', ['only' => ['edit', 'update']]);
    }

    public function index(Request $request): View
    {
        $query = User::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        return view('admin.users.index', [
            'users' => $query->latest()->paginate(10),
        ]);
    }

    public function show(User $user): View
    {
        $user->loadCount('orders')->loadSum('orders', 'total_amount');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
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

        return redirect()->route('admin.users.index')->with('status', "User <strong>{$user->name}</strong> has been updated successfully.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->withErrors(['user' => 'You cannot archive your own account.']);
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', "User <strong>{$name}</strong> has been archived.");
    }
}
