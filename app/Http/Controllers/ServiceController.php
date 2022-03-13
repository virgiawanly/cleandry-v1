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
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Datatable
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
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Outlet  $outlet
     * @param  \Illuminate\Http\Request  $request
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
            'message' => 'Layanan berhasil ditambahkan'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
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
     * Save services data as excel file.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \App\Exports\ServicesExport
     */
    public function exportExcel(Outlet $outlet)
    {
        return (new ServicesExport)->whereOutlet($outlet->id)->download('Layanan-' . date('d-m-Y') . '.xlsx');
    }

    /**
     * Save services data as pdf file.
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
     * Import services data from xlsx file.
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
     * Download excel template.
     *
     * @return \Illuminate\Support\Facades\Storage
     */
    public function downloadTemplate()
    {
        return Storage::download('templates/Import_layanan_cleandry.xlsx');
    }
}
