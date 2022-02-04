<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('members.index', [
            'title' => 'Kelola Member',
            'breadcrumbs' => [
                [
                    'href' => '/members',
                    'label' => 'Member'
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
        $members = Member::where('outlet_id', Auth::user()->outlet_id)->get();

        return DataTables::of($members)
            ->addIndexColumn()
            ->addColumn('actions', function ($member) {
                $editBtn = '<button onclick="editHandler(' . "'" . route('members.update', $member->id) . "'" . ')" class="btn btn-warning mx-1 mb-1">
                    <i class="fas fa-edit mr-1"></i>
                    <span>Edit member</span>
                </button>';
                $deleteBtn = '<button class="btn btn-danger mx-1 mb-1">
                    <i class="fas fa-trash mr-1"></i>
                    <span>Hapus member</span>
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
            'phone' => 'required|max:16|unique:members,phone',
            'email' => 'email|unique:members,email',
            'gender' => 'required|in:M,F',
            'address' => 'required',
        ]);

        $payload = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'gender' => $request->gender,
            'address' => $request->address,
        ];

        Auth::user()->outlet->members()->create($payload);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi member berhasil'
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function show(Member $member)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data member',
            'member' => $member
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Member $member)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|max:16|unique:members,phone,' . $member->id,
            'email' => 'email|unique:members,email,' . $member->id,
            'gender' => 'required|in:M,F',
            'address' => 'required',
        ]);

        $payload = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'gender' => $request->gender,
            'address' => $request->address,
        ];

        $member->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'Member berhasil diupdate'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function destroy(Member $member)
    {
        //
    }
}
