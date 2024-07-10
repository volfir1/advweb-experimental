<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    public function fetchUsers(Request $request)
    {
        $query = User::leftJoin('customers', 'users.id', '=', 'customers.user_id')
            ->select('users.*', 'customers.fname', 'customers.lname', 'customers.contact', 'customers.address');

        if ($request->has('search')) {
            $searchValue = $request->input('search');
            $query->where(function($q) use ($searchValue) {
                $q->where('users.name', 'like', "%{$searchValue}%")
                    ->orWhere('users.email', 'like', "%{$searchValue}%")
                    ->orWhere('customers.fname', 'like', "%{$searchValue}%")
                    ->orWhere('customers.lname', 'like', "%{$searchValue}%");
            });
        }

        $totalRecords = $query->count();
        $filteredRecords = $totalRecords;

        $users = $query->skip($request->input('start'))
            ->take($request->input('length'))
            ->get();

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'profile_image' => $user->profile_image,
                    'name' => $user->name,
                    'email' => $user->email,
                    'active_status' => $user->active_status,
                    'fname' => $user->fname,
                    'lname' => $user->lname,
                    'contact' => $user->contact,
                    'address' => $user->address,
                ];
            })
        ]);
    }

    public function getEditUserData($id)
    {
        try {
            $user = User::with('customer')->findOrFail($id);

            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'first_name' => $user->customer->fname,
                'last_name' => $user->customer->lname,
                'email' => $user->email,
                'contact' => $user->customer->contact,
                'address' => $user->customer->address,
                'active_status' => $user->active_status ? 1 : 0,
                'profile_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'message' => 'An error occurred while fetching the user data.',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function updateUserData(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('users')->ignore($request->id),
        ],
        'contact' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'active_status' => 'required|in:1,0',
        'password' => 'nullable|string|min:8',
        'profile_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    DB::beginTransaction();

    try {
        $user = User::findOrFail($request->id);

        // Update user data
        $user->name = $request->name;
        $user->email = $request->email;
        $user->active_status = $request->active_status == 1 ? 1 : 0;

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update related customer data
        $customer = $user->customer;
        $customer->fname = $request->first_name;
        $customer->lname = $request->last_name;
        $customer->contact = $request->contact;
        $customer->address = $request->address;
        $user->customer()->save($customer);

        DB::commit();

        return response()->json([
            'success' => 'User updated successfully'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'error' => [
                'message' => 'An error occurred while updating the user.',
                'details' => $e->getMessage()
            ]
        ], 500);
    }
}

    public function saveUser(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:8', // If updating, password can be nullable
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'profile_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',

        ]);

        // Check if it's an update or create request
        $user = $request->id ? User::find($request->id) : new User();

        // Update the user data
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->active_status = $request->active_status ? 1 : 0;

        // Save the user
        $user->save();

        // Save or update the related customer data
        $customer = $user->customer ?: new Customer();
        $customer->user_id = $user->id;
        $customer->fname = $request->first_name;
        $customer->lname = $request->last_name;
        $customer->contact = $request->contact;
        $customer->address = $request->address;

        // Save the customer
        $customer->save();

        // Return a response
        return response()->json(['success' => 'User saved successfully!']);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        DB::beginTransaction();

        try {
            // Delete related records if necessary
            // Example: $user->profile()->delete();

            $user->delete();

            DB::commit();

            return response()->json([
                'success' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => [
                    'message' => 'An error occurred while deleting the user.',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function storeUser(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($request->id ?? 'NULL') . ',id',
            'password' => $request->id ? 'nullable|string|min:6' : 'required|string|min:6',
            'active_status' => 'boolean',
            'profile_image' => 'nullable|image|max:2048',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if it's an update or create request
        $user = $request->id ? User::find($request->id) : new User();

        // Update the user data
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->active_status = $request->active_status ? 1 : 0;
        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image');
            $profileImagePath = $profileImage->store('profile_images', 'public');
            $user->profile_image = $profileImagePath;
        }

        // Save the user
        $user->save();

        // Save or update the related customer data
        $customer = $user->customer ?: new Customer();
        $customer->user_id = $user->id;
        $customer->fname = $request->first_name;
        $customer->lname = $request->last_name;
        $customer->contact = $request->contact;
        $customer->address = $request->address;

        // Save the customer
        $customer->save();

        // Return a response
        return response()->json(['success' => 'User saved successfully!']);
    }
}

