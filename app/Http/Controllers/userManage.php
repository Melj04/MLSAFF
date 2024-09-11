<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class userManage extends Controller
{
       // Show all users
       public function index()
       {
           $users = User::all();
           return view('auth.manage_user', compact('users'));
       }

       // Update user role and status
       public function update(Request $request, $id)
       {
           // Validate the form input
           $request->validate([
               'role' => 'required|integer',
               'status' => 'required|boolean',
           ]);

           // Find the user to be updated
           $user = User::findOrFail($id);

           // Prevent updating user with ID 1 or 0
           if ($user->id == 1 || $user->id == 0) {
               return redirect()->route('admin.users')->withErrors(['error' => 'This user cannot be updated.']);
           }

           // Update user role and status
           $user->role = $request->input('role');
           $user->status = $request->input('status');
           $user->save();

           return redirect()->route('admin.users')->with('success', 'User updated successfully.');
       }

       // Verify the password before allowing the update
       public function verifyPassword(Request $request, $id)
       {
           // Validate the password input
           $request->validate([
               'password' => 'required|string',
           ]);

           // Get the authenticated admin
           $admin = Auth::user();

           // Verify admin's password
    if (!Hash::check($request->password, $admin->password)) {
        // Redirect back with error and session data for the modal
        return back()->withErrors(['password' => 'Your password is incorrect.'])
                     ->withInput()
                     ->with('id', $id); // Store user ID in session
    }
           // Proceed to update the user directly after password verification
           return $this->update($request, $id);
       }
   }
