<?php

namespace App\Http\Controllers;

use App\Exports\ItemsExport;
use App\Imports\ItemsImport;
use App\Models\Item;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FacadesResponse;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class ItemController extends Controller
{
    /**
     * Menampilkan halaman data barang.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('items.index', [
            'title' => 'Kelola Barang',
            'breadcrumbs' => [
                [
                    'href' => '/items',
                    'label' => 'Data barang'
                ]
            ],
        ]);
    }

    /**
     * Mendapatkan data barang untuk datatable.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable()
    {
        $items = Item::all();

        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('update_status', function ($item) {
                $dropdown = '<select class="item-status form-control" data-update-url="' . route('items.updateStatus', $item->id) . '">';
                $dropdown .= '<option value="submission"';
                if ($item->status === 'submission') $dropdown .= ' selected';
                $dropdown .= '>Diajukan</option>';

                $dropdown .= '<option value="out_of_stock"';
                if ($item->status === 'out_of_stock') $dropdown .= ' selected';
                $dropdown .= '>Habis</option>';

                $dropdown .= '<option value="available"';
                if ($item->status === 'available') $dropdown .= ' selected';
                $dropdown .= '>Tersedia</option>';
                $dropdown .= '</select>';
                return $dropdown;
            })
            ->addColumn('actions', function ($item) {
                $editBtn = '<button class="btn btn-warning mx-1 mb-1 edit-item-button" data-edit-item-url="' . route('items.update', $item->id) . '">
                    <i class="fas fa-edit mr-1"></i>
                    <span>Edit</span>
                </button>';
                $deleteBtn = '<button class="btn btn-danger mx-1 mb-1 delete-item-button" data-delete-item-url="' . route('items.destroy', $item->id) . '">
                    <i class="fas fa-trash mr-1"></i>
                    <span>Hapus</span>
                </button>';
                return $editBtn . $deleteBtn;
            })->rawColumns(['actions', 'update_status'])->make(true);
    }

    /**
     * Menyimpan data barang baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'qty' => 'required|numeric',
            'price' => 'required|numeric',
            'buy_date' => 'required',
            'supplier' => 'required',
            'status' => 'required|in:submission,out_of_stock,available',
        ]);

        $payload = [
            'name' => $request->name,
            'qty' => $request->qty,
            'price' => $request->price,
            'buy_date' => $request->buy_date,
            'supplier' => $request->supplier,
            'status' => $request->status,
        ];

        try {
            Item::create($payload);

            return response()->json([
                'message' => 'Data barang berhasil ditambahkan',
            ], Response::HTTP_OK);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Menampilkan data barang berdasarkan id barang.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        $item->buy_date = date('Y-m-d', strtotime($item->buy_date));

        return response()->json([
            'message' => 'Data barang',
            'item' => $item
        ], Response::HTTP_OK);
    }

    /**
     * Mengupdate data barang berdasarkan id barang.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required',
            'qty' => 'required|numeric',
            'price' => 'required|numeric',
            'buy_date' => 'required',
            'supplier' => 'required',
            'status' => 'required|in:submission,out_of_stock,available',
        ]);

        $payload = [
            'name' => $request->name,
            'qty' => $request->qty,
            'price' => $request->price,
            'buy_date' => $request->buy_date,
            'supplier' => $request->supplier,
            'status' => $request->status,
        ];

        if ($item->status !== $request->status) {
            $payload['status_updated_at'] = now();
        }

        $item->update($payload);

        return response()->json([
            'message' => 'Data barang berhasil diupdate'
        ], Response::HTTP_OK);
    }

    /**
     * Mengupdate status ketersediaan barang.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Item $item)
    {
        $request->validate([
            'status' => 'required|in:submission,out_of_stock,available',
        ]);

        $payload = [
            'status' => $request->status,
        ];

        if ($item->status !== $request->status) {
            $payload['status_updated_at'] = now();
        }

        $item->update($payload);

        return response()->json([
            'message' => 'Status diupdate',
            'status_updated_at' => date('Y-m-d h:i:s', strtotime($item->status_updated_at)),
        ], Response::HTTP_OK);
    }

    /**
     * Menghapus data barang di database berdasarkan id barang.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        if ($item->delete()) {
            return response()->json([
                'message' => 'Data barang berhasil dihapus'
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
        return (new ItemsExport)->download('Data-barang-' . date('d-m-Y') . '.xlsx');
    }

    /**
     * Export data barang sebagai file Pdf.
     *
     * @return \Barryvdh\DomPDF\Facade\Pdf
     */
    public function exportPDF()
    {
        $items = Item::all();

        $pdf = Pdf::loadView('items.pdf', ['items' => $items]);
        return $pdf->stream('Data-barang-' . date('dmY') . '.pdf');
    }

    /**
     * Import data barang dari file excel (.xlsx).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse;
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx'
        ]);

        Excel::import(new ItemsImport, $request->file('file_import'));

        return response()->json([
            'message' => 'Import data berhasil'
        ], Response::HTTP_OK);
    }

    /**
     * Download template untuk import data barang.
     *
     * @return \Illuminate\Support\Facades\Storage
     */
    public function downloadTemplate()
    {
        return FacadesResponse::download(public_path() . "/templates/Import_barang_cleandry.xlsx");
    }
}
