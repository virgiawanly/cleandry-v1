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
            ->addColumn('actions', function () {
                $editBtn = '<button class="btn btn-warning mx-1 mb-1">
                    <i class="fas fa-edit mr-1"></i>
                    <span>Edit user</span>
                </button>';
                $deleteBtn = '<button class="btn btn-danger mx-1 mb-1">
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
}
