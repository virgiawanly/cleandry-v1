<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Http\Request;
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
        return view('users.index', [
            'title' => 'Kelola Users',
            'breadcrumbs' => [
                [
                    'href' => '/users',
                    'label' => 'Users'
                ]
            ],
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
}
