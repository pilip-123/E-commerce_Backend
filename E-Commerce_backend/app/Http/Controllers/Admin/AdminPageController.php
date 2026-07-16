<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\User;
use App\Models\Order;
use App\Models\DiscountCode;
use App\Notifications\VipDiscountNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:customers.view', ['only' => ['customers']]);
        $this->middleware('permission:vipcodes.view', ['only' => ['vipCodes']]);
        $this->middleware('permission:vipcodes.generate', ['only' => ['generateVipCode']]);
        $this->middleware('permission:vipcodes.delete', ['only' => ['deleteVipCode']]);
    }

    public function customers(Request $request): View
    {
        $query = User::withCount('orders')
            ->withSum('orders', 'total_amount');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        return view('admin.customers', [
            'users' => $query->latest()->paginate(10),
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

        return redirect()->route('admin.profile')->with('status', 'Your profile has been updated successfully.');
    }

    public function vipCodes(): View
    {
        $qualifyingCustomers = User::where('role', 'customer')
            ->whereHas('orders')
            ->withCount('orders')
            ->withCount(['orders as week_orders' => function ($q) {
                $q->where('created_at', '>=', now()->subWeek());
            }])
            ->withSum('orders', 'total_amount')
            ->get()
            ->filter(function ($user) {
                return ($user->orders_sum_total_amount ?? 0) >= 500;
            })
            ->sortByDesc('orders_sum_total_amount')
            ->values();

        $codes = DiscountCode::latest()->paginate(10);

        return view('admin.promotions.vip-codes', [
            'qualifyingCustomers' => $qualifyingCustomers,
            'codes' => $codes,
        ]);
    }

    public function generateVipCode(Request $request): RedirectResponse
    {
        $request->validate([
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0.01',
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:users,id',
        ]);

        $code = 'VIP-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

        // Send notification only to selected customers
        $customers = User::whereIn('id', $request->customer_ids)->get();
        $customerNames = $customers->pluck('name')->toArray();
        $customerCount = $customers->count();

        foreach ($customers as $customer) {
            $customer->notify(new VipDiscountNotification(
                $code,
                $request->discount_type,
                (string) $request->discount_value
            ));
        }

        // Store code in database
        DiscountCode::create([
            'code' => $code,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'sent_to' => $customerNames,
            'sent_count' => $customerCount,
        ]);

        return redirect()->route('admin.promotions.vip-codes')
            ->with('generated_code', $code)
            ->with('sent_count', $customerCount)
            ->with('status', "VIP code <strong>{$code}</strong> generated and sent to <strong>{$customerCount}</strong> customer(s).");
    }

    public function deleteVipCode($id): RedirectResponse
    {
        DiscountCode::findOrFail($id)->delete();

        return redirect()->route('admin.promotions.vip-codes')
            ->with('status', 'VIP discount code has been archived.');
    }

    public function updateNotificationPreferences(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'in:order_updates,new_promotions,low_stock,new_products,review_alerts'],
            'value' => ['required', 'in:0,1'],
        ]);

        $user = auth()->user();
        $prefs = $user->notification_preferences ?? [];
        $prefs[$validated['key']] = (bool) $validated['value'];
        $user->update(['notification_preferences' => $prefs]);

        return response()->json(['success' => true, 'message' => 'Preference saved.']);
    }

    public function permissions(): View
    {
        $roles = User::ROLES;
        $permissions = Permission::all();
        $permissionGroups = $permissions->groupBy('group');

        $rolePermissionIds = [];
        foreach ($roles as $role) {
            $rolePermissionIds[$role] = PermissionRole::where('role', $role)
                ->pluck('permission_id')
                ->toArray();
        }

        return view('admin.permissions', [
            'roles' => $roles,
            'permissionGroups' => $permissionGroups,
            'rolePermissionIds' => $rolePermissionIds,
        ]);
    }

    public function updatePermissions(Request $request): RedirectResponse
    {
        $roles = User::ROLES;

        DB::table('permission_role')->truncate();

        foreach ($roles as $role) {
            if ($role === User::ROLE_ADMIN) {
                continue;
            }

            $selected = $request->input("perms.{$role}", []);

            foreach ($selected as $permissionId) {
                PermissionRole::create([
                    'permission_id' => $permissionId,
                    'role' => $role,
                ]);
            }
        }

        return redirect()->route('admin.permissions')
            ->with('status', 'Role permissions have been updated successfully.');
    }
}
