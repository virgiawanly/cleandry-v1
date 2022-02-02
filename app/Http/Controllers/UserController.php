<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $outlets = Outlet::all();

        return view('users.index', [
            'title' => 'Kelola Users',
            'breadcrumbs' => [
                [
                    'href' => '/users',
                    'label' => 'Users'
                ]
            ],
            'outlets' => $outlets
        ]);
    }

    /**
     * Return data for DataTables.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable()
    {
        $users = User::with('outlet')->regular()->get();

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('actions', function ($user) {
                $editBtn = '<button onclick="editHandler(' . "'" . route('users.update', $user->id) . "'" . ')" class="btn btn-warning mx-1 mb-1">
                    <i class="fas fa-edit mr-1"></i>
                    <span>Edit user</span>
                </button>';
                $deleteBtn = '<button onclick="deleteHandler(' . "'" . route('users.destroy', $user->id) . "'" . ')" class="btn btn-danger mx-1 mb-1">
                    <i class="fas fa-trash mr-1"></i>
                    <span>Hapus user</span>
                </button>';
                return $editBtn . $deleteBtn;
            })->rawColumns(['actions'])->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'max:16|unique:users,phone',
            'password' => 'required|min:5|confirmed',
            'password_confirmation' => 'required',
            'role' => 'required|in:admin,owner,cashier',
            'outlet_id' => 'required|exists:outlets,id',
        ]);

        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => bcrypt($request->password),
            'outlet_id' => $request->outlet_id,
        ];

        User::create($payload);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi user berhasil'
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data user',
            'user' => $user
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'max:16|unique:users,phone,' . $user->id,
            'role' => 'required|in:admin,owner,cashier',
            'outlet_id' => 'required|exists:outlets,id',
        ]);

        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'outlet_id' => $request->outlet_id,
        ];

        if ($request->has('password') && $request->password != '') {
            $request->validate([
                'password' => 'required|min:5|confirmed',
                'password_confirmation' => 'required',
            ]);
            $payload['password'] = bcrypt($request->password);
        }

        $user->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diupdate'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->delete()) {
            return response()->json([
                'message' => 'User berhasil dihapus'
            ], Response::HTTP_OK);
        };

        return response()->json([
            'message' => 'Terjadi kesalahan'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
