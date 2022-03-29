<?php

namespace App\Http\Controllers;

use App\Exports\ServicesExport;
use App\Imports\ServicesImport;
use App\Models\Outlet;
use App\Models\Service;
use App\Models\ServiceType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FacadesResponse;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class ServiceController extends Controller
{
    /**
     * Menampilkan halaman manajemen layanan.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function index(Outlet $outlet)
    {
        $types = ServiceType::all();

        return view('services.index', [
            'title' => 'Kelola Layanan',
            'breadcrumbs' => [
                [
                    'href' => '/services',
                    'label' => 'Layanan'
                ]
            ],
            'types' => $types,
            'outlet' => $outlet
        ]);
    }

    /**
     * Mendapatkan data layanan untuk datatable.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function datatable(Outlet $outlet)
    {
        $services = Service::with('type', 'outlet')->where('outlet_id', $outlet->id)->get();

        return DataTables::of($services)
            ->addIndexColumn()
            ->addColumn('actions', function ($service) use ($outlet) {
                $editBtn = '<button class="btn btn-info mx-1 edit-service-button" data-edit-service-url="' . route('services.update', [$outlet->id,  $service->id]) . '">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </button>';
                $deletBtn = '<button class="btn btn-danger mx-1 delete-service-button" data-delete-service-url="' . route('services.destroy', [$outlet->id,  $service->id]) . '">
                    <i class="fas fa-trash"></i>
                    <span>Hapus</span>
                </button>';
                return $editBtn . $deletBtn;
            })->rawColumns(['actions'])->make(true);
    }

    /**
     * Menyimpan data layanan baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Outlet $outlet)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'type_id' => 'required|exists:service_types,id',
            'unit' => 'required|in:m,kg,pcs',
        ]);

        $payload = [
            'name' => $request->name,
            'price' => $request->price,
            'type_id' => $request->type_id,
            'unit' => $request->unit,
        ];

        $outlet->services()->create($payload);

        return response()->json([
            'message' => 'Layanan berhasil ditambahkan'
        ], Response::HTTP_OK);
    }

    /**
     * Mendapatkan data layanan berdasarkan id tertentu.
     *
     * @param  \App\Models\Outlet  $outlet
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Outlet $outlet, Service $service)
    {
        $service->load(['type']);
        return response()->json([
            'message' => 'Data layanan',
            'service' => $service
        ], Response::HTTP_OK);
    }

    /**
     * Mengupdate data layanan berdasarkan id tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Outlet $outlet, Service $service)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'type_id' => 'required|exists:service_types,id',
            'unit' => 'required|in:m,kg,pcs',
        ]);

        $payload = [
            'name' => $request->name,
            'price' => $request->price,
            'type_id' => $request->type_id,
            'unit' => $request->unit,
        ];

        $service->update($payload);

        return response()->json([
            'message' => 'Layanan berhasil diupdate'
        ], Response::HTTP_OK);
    }

    /**
     * Menghapus data layanan berdasarkan id tertentu.
     *
     * @param  \App\Models\Outlet  $outlet
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Outlet $outlet, Service $service)
    {
        if ($service->delete()) {
            return response()->json([
                'message' => 'Layanan berhasil dihapus'
            ], Response::HTTP_OK);
        };

        return response()->json([
            'message' => 'Terjadi kesalahan'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Export data ke file excel (.xlsx).
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Outlet $outlet)
    {
        return (new ServicesExport)->whereOutlet($outlet->id)->download('Layanan-' . date('d-m-Y') . '.xlsx');
    }

    /**
     * Export data ke file pdf.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Barryvdh\DomPDF\Facade\Pdf
     */
    public function exportPDF(Outlet $outlet)
    {
        $services = Service::where('outlet_id', $outlet->id)->with('outlet')->get();

        $pdf = Pdf::loadView('services.pdf', ['services' => $services, 'outlet' => $outlet]);
        return $pdf->stream('Layanan-' . date('dmY') . '.pdf');
    }

    /**
     * Import data dari file excel (.xlsx).
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

        Excel::import(new ServicesImport, $request->file('file_import'));

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
        return FacadesResponse::download(public_path() . "/templates/Import_layanan_cleandry.xlsx");
    }
}
