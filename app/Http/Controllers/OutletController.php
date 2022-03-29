<?php

namespace App\Http\Controllers;

use App\Exports\OutletsExport;
use App\Imports\OutletsImport;
use App\Models\Outlet;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response as FacadesResponse;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class OutletController extends Controller
{
    /**
     * Menampilkan halaman manajemen outlet.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('outlets.index', [
            'title' => 'Kelola Outlet',
            'breadcrumbs' => [
                [
                    'href' => '/outlets',
                    'label' => 'Outlets'
                ]
            ],
        ]);
    }

    /**
     * Menampilkan halaman pemilihan outlet untuk admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function selectOutlet()
    {
        $outlets = Outlet::all();
        return view('outlets.select_outlet', [
            'title' => 'Pilih Outlet',
            'breadcrumbs' => [
                [
                    'href' => '/select-outlet',
                    'label' => 'Pilih Outlet'
                ]
            ],
            'outlets' => $outlets,
        ]);
    }

    /**
     * Menyimpan session outlet aktif yang dipilih admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setOutlet(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required',
        ]);

        if (Auth::user()->role === 'admin') {
            $outlet = Outlet::find($request->outlet_id);
            $request->session()->put('outlet', $outlet);
        }

        return redirect()->to('/');
    }

    /**
     * Mendapatkan data outlet.
     *
     * @return \Illuminate\Http\Response
     */
    public function data()
    {
        $outlets = Outlet::all();

        return response()->json([
            'message' => 'Data outlet',
            'outlets' => $outlets,
        ]);
    }

    /**
     * Mendapatkan data outlet untuk datatable.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable()
    {
        $outlets = Outlet::all();

        return DataTables::of($outlets)
            ->addIndexColumn()
            ->addColumn('actions', function ($outlet) {
                $editBtn = '<button onclick="editHandler(' . "'" . route('outlets.update', $outlet->id) . "'" . ')" class="btn btn-warning mx-1 mb-1">
                    <i class="fas fa-edit mr-1"></i>
                    <span>Edit</span>
                </button>';
                $deleteBtn = '<button onclick="deleteHandler(' . "'" . route('outlets.destroy', $outlet->id) . "'" . ')" class="btn btn-danger mx-1 mb-1">
                    <i class="fas fa-trash mr-1"></i>
                    <span>Hapus</span>
                </button>';
                return $editBtn . $deleteBtn;
            })->rawColumns(['actions'])->make(true);
    }

    /**
     * Menyimpan data outlet baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|max:24',
            'address' => 'required'
        ]);

        $payload = [
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        Outlet::create($payload);

        return response()->json([
            'message' => 'Outlet berhasil dibuat'
        ], Response::HTTP_OK);
    }

    /**
     * Mendapatkan data outlet berdasarkan id tertentu.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function show(Outlet $outlet)
    {
        return response()->json([
            'message' => 'Data outlet',
            'outlet' => $outlet
        ], Response::HTTP_OK);
    }

    /**
     * Mengupdate data outlet di database berdasarkan id tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Outlet $outlet)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|max:24',
            'address' => 'required'
        ]);

        $outlet->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return response()->json([
            'message' => 'Outlet berhasil diupdate'
        ], Response::HTTP_OK);
    }

    /**
     * Menghapus data outlet berdasarkan id terntentu.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Outlet $outlet)
    {
        if ($outlet->delete()) {
            return response()->json([
                'message' => 'Outlet berhasil dihapus'
            ], Response::HTTP_OK);
        };

        return response()->json([
            'message' => 'Terjadi kesalahan'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Export data ke file excel (.xlsx).
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel()
    {
        return (new OutletsExport)->download('Outlet-' . date('d-m-Y') . '.xlsx');
    }

    /**
     * Export data ke file pdf.
     *
     * @return \Barryvdh\DomPDF\Facade\Pdf
     */
    public function exportPDF()
    {
        $outlets = Outlet::all();

        $pdf = Pdf::loadView('outlets.pdf', ['outlets' => $outlets]);
        return $pdf->stream('Outlet-' . date('dmY') . '.pdf');
    }


    /**
     * Import data dari file excel (.xlsx).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse;
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx'
        ]);

        Excel::import(new OutletsImport, $request->file('file_import'));

        return response()->json([
            'message' => 'Import data berhasil'
        ], Response::HTTP_OK);
    }

    /**
     * Download template import excel.
     *
     * @return \Illuminate\Support\Facades\Storage
     */
    public function downloadTemplate()
    {
        return FacadesResponse::download(public_path() . "/templates/Import_outlet_cleandry.xlsx");
    }
}
