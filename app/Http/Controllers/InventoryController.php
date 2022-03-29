<?php

namespace App\Http\Controllers;

use App\Exports\InventoriesExport;
use App\Imports\InventoriesImport;
use App\Models\Inventory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FacadesResponse;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class InventoryController extends Controller
{
    /**
     * Menampilkan halaman barang inventaris.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('inventories.index', [
            'title' => 'Kelola Barang Inventaris',
            'breadcrumbs' => [
                [
                    'href' => '/inventories',
                    'label' => 'Barang Inventaris'
                ]
            ],
        ]);
    }

    /**
     * Mendapatkan data inventaris untuk datatable.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable()
    {
        $inventories = Inventory::all();

        return DataTables::of($inventories)
            ->addIndexColumn()
            ->addColumn('actions', function ($inventory) {
                $editBtn = '<button class="btn btn-warning mx-1 mb-1 edit-inventory-button" data-edit-inventory-url="' . route('inventories.update', $inventory->id) . '">
                    <i class="fas fa-edit mr-1"></i>
                    <span>Edit</span>
                </button>';
                $deleteBtn = '<button class="btn btn-danger mx-1 mb-1 delete-inventory-button" data-delete-inventory-url="' . route('inventories.destroy', $inventory->id) . '">
                    <i class="fas fa-trash mr-1"></i>
                    <span>Hapus</span>
                </button>';
                return $editBtn . $deleteBtn;
            })->rawColumns(['actions'])->make(true);
    }

    /**
     * Menyimpan data inventaris baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'brand' => 'required',
            'qty' => 'required|numeric',
            'condition' => 'required|in:good,damaged,broken',
            'procurement_date' => 'required',
        ]);

        $payload = [
            'name' => $request->name,
            'brand' => $request->brand,
            'qty' => $request->qty,
            'condition' => $request->condition,
            'procurement_date' => $request->procurement_date,
        ];

        Inventory::create($payload);

        return response()->json([
            'message' => 'Inventaris berhasil ditambahkan'
        ], Response::HTTP_OK);
    }

    /**
     * Mendapatkan data inventaris berdasarkan id tertentu.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(Inventory $inventory)
    {
        return response()->json([
            'message' => 'Data barang inventaris',
            'inventory' => $inventory
        ], Response::HTTP_OK);
    }

    /**
     * Mengupdate data inventaris di database berdasarkan id tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'name' => 'required',
            'brand' => 'required',
            'qty' => 'required|numeric',
            'condition' => 'required|in:good,damaged,broken',
            'procurement_date' => 'required',
        ]);

        $payload = [
            'name' => $request->name,
            'brand' => $request->brand,
            'qty' => $request->qty,
            'condition' => $request->condition,
            'procurement_date' => $request->procurement_date,
        ];

        $inventory->update($payload);

        return response()->json([
            'message' => 'Barang inventaris berhasil diupdate'
        ], Response::HTTP_OK);
    }

    /**
     * Menghapus data inventaris di database.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        if ($inventory->delete()) {
            return response()->json([
                'message' => 'Barang inventaris berhasil dihapus'
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
        return (new InventoriesExport)->download('Inventaris-' . date('d-m-Y') . '.xlsx');
    }

    /**
     * Export data ke file pdf.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPDF()
    {
        $inventories = Inventory::all();

        $pdf = Pdf::loadView('inventories.pdf', ['inventories' => $inventories]);
        return $pdf->download('Inventaris-' . date('dmY') . '.pdf');
    }

    /**
     * Import data dari file excel (.xlsx).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response;
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx'
        ]);

        Excel::import(new InventoriesImport, $request->file('file_import'));

        return response()->json([
            'message' => 'Import data berhasil'
        ], Response::HTTP_OK);
    }

    /**
     * Download template untuk import excel.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate()
    {
        return FacadesResponse::download(public_path() . "/templates/Import_inventaris_cleandry.xlsx");
    }
}
