<?php

namespace App\Http\Controllers;



use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Http;
use App\Models\M_user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Users extends Controller
{
    public function index(Request $request)
    {
        // $data = M_user::all(); // Ensure the model is loaded
        // return response()->json([
        //     'data' => $data->map(function ($user) {
        //         return [
        //             'id' => $user->id,
        //             'name' => $user->name,
        //             'email' => $user->email,
        //             'action' => '<a href="/users/' . $user->id . '" class="btn btn-sm btn-info">View</a>'
        //         ];
        //     })
        // ]);
        $response = Http::get('https://jsonplaceholder.typicode.com/users');
        $users = collect($response->json()); // Convert to Laravel collection
        // // Convert to Laravel collection
        // if ($request->ajax()) {
        //     // Fetch data from external API
        //     $response = Http::get('https://jsonplaceholder.typicode.com/users');
        //     $users = collect($response->json()); // Convert to Laravel collection

        //     return DataTables::of($users)
        //         ->addIndexColumn()
        //         ->addColumn('company', function ($row) {
        //             return $row['company']['name'] ?? '-';
        //         })
        //         ->addColumn('action', function ($row) {
        //             return '<a href="/users/' . $row['id'] . '" class="btn btn-sm btn-info">View</a>';
        //         })
        //         ->rawColumns(['action'])
        //         ->make(true);
        // }
        return response()->json([
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'company' => $user['company']['name'] ?? '-',
                    'action' => '<a href="/users/' . $user['id'] . '" class="btn btn-sm btn-info">View</a>'
                ];
            })
        ]);
        // // return view('remote-users.index');
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = M_user::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        return response()->json(['success' => true, 'user' => $user]);
    }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Attempt to authenticate using email/password
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Generate Sanctum API token
            $token = $user->createToken('authToken')->plainTextToken;

            // Return token and user info
            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user
            ]);
        }

        // Failed authentication response
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }
}
