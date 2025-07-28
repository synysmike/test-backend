<?php

namespace App\Http\Controllers;



use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Users extends Controller
{
    public function index(Request $request)
    {
        $data = User::all(); // Ensure the model is loaded
        return response()->json([
            'data' => $data->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'action' => '<a href="/users/' . $user->id . '" class="btn btn-sm btn-info">View</a>'
                ];
            })
        ]);
        // $response = Http::get('https://jsonplaceholder.typicode.com/users');
        // $users = collect($response->json()); // Convert to Laravel collection
        // // Convert to Laravel collection
        if ($request->ajax()) {

            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('company', function ($row) {
                //     return $row['company']['name'] ?? '-';
                // })
                ->addColumn('action', function ($row) {
                    return '<a href="/users/' . $row['id'] . '" class="btn btn-sm btn-info">View</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        // return response()->json([
        //     'data' => $users->map(function ($user) {
        //         return [
        //             'id' => $user['id'],
        //             'name' => $user['name'],
        //             'company' => $user['company']['name'] ?? '-',
        //             'action' => '<a href="/users/' . $user['id'] . '" class="btn btn-sm btn-info">View</a>'
        //         ];
        //     })
        // ]);
        // // return view('remote-users.index');
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        return response()->json(['success' => true, 'user' => $user]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Optional: revoke old tokens if needed
        // $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user
        ]);
    }
}
