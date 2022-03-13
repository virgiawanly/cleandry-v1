<?php

namespace App\Http\Controllers;

use App\Exports\MembersExport;
use App\Imports\MembersImport;
use App\Models\Member;
use App\Models\Outlet;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function index(Outlet $outlet)
    {
        return view('members.index', [
            'title' => 'Kelola Member',
            'breadcrumbs' => [
                [
                    'href' => '/members',
                    'label' => 'Member'
                ]
            ],
            'outlet' => $outlet,
        ]);
    }

    /**
     * Return data for DataTables.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function datatable(Outlet $outlet)
    {
        $members = Member::where('outlet_id', $outlet->id)->get();

        return DataTables::of($members)
            ->addIndexColumn()
            ->addColumn('actions', function ($member) use ($outlet) {
                $editBtn = '<button onclick="editHandler(' . "'" . route('members.update', [$outlet->id, $member->id]) . "'" . ')" class="btn btn-warning mx-1 mb-1">
                    <i class="fas fa-edit mr-1"></i>
                    <span>Edit member</span>
                </button>';
                $deleteBtn = '<button onclick="deleteHandler(' . "'" . route('members.destroy', [$outlet->id, $member->id]) . "'" . ')" class="btn btn-danger mx-1 mb-1">
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
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Outlet $outlet)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|max:24|unique:members,phone',
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

        $outlet->members()->create($payload);

        return response()->json([
            'message' => 'Registrasi member berhasil'
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Outlet  $outlet
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function show(Outlet $outlet, Member $member)
    {
        return response()->json([
            'message' => 'Data member',
            'member' => $member
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Outlet $outlet, Member $member)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|max:24|unique:members,phone,' . $member->id,
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
            'message' => 'Member berhasil diupdate'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Outlet  $outlet
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function destroy(Outlet $outlet, Member $member)
    {
        if ($member->delete()) {
            return response()->json([
                'message' => 'Member berhasil dihapus'
            ], Response::HTTP_OK);
        };

        return response()->json([
            'message' => 'Terjadi kesalahan'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Save members data as excel file.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \App\Exports\MembersExport
     */
    public function exportExcel(Outlet $outlet)
    {
        return (new MembersExport)->whereOutlet($outlet->id)->download('Member-' . date('d-m-Y') . '.xlsx');
    }

    /**
     * Save members data as pdf file.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Barryvdh\DomPDF\Facade\Pdf
     */
    public function exportPDF(Outlet $outlet)
    {
        $members = Member::where('outlet_id', $outlet->id)->with('outlet')->get();

        $pdf = Pdf::loadView('members.pdf', ['members' => $members, 'outlet' => $outlet]);
        return $pdf->stream('Layanan-' . date('dmY') . '.pdf');
    }

    /**
     * Import members data from xlsx file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\RedirectResponse;
     */
    public function importExcel(Request $request, Outlet $outlet)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx'
        ]);

        Excel::import(new MembersImport, $request->file('file_import'));

        return response()->json([
            'message' => 'Import data berhasil'
        ], Response::HTTP_OK);
    }

    /**
     * Download excel template.
     *
     * @return \Illuminate\Support\Facades\Storage
     */
    public function downloadTemplate()
    {
        return Storage::download('templates/Import_member_cleandry.xlsx');
    }
}
