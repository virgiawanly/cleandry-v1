<?php

namespace App\Http\Controllers;

use App\Exports\ItemUsesExport;
use App\Imports\ItemUsesImport;
use App\Models\Item;
use App\Models\ItemUses;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FacadesResponse;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class ItemUsesController extends Controller
{
    /**
     * Menampilkan halaman data penggunaan barang.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('item_uses.index', [
            'title' => 'Data Penggunaan Barang',
            'breadcrumbs' => [
                [
                    'href' => '/item-uses',
                    'label' => 'Penggunaan Barang'
                ]
            ],
            'items' => Item::all(),
        ]);
    }

    /**
     * Mendapatkan data penggunaan barang untuk datatable.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable()
    {
        $itemUses = ItemUses::all();

        return DataTables::of($itemUses)
            ->addIndexColumn()
            ->editColumn('start_use', function ($uses) {
                return date('Y/m/d h:i', strtotime($uses->start_use));
            })
            ->editColumn('end_use', function ($uses) {
                if ($uses->end_use) {
                    return date('Y/m/d h:i', strtotime($uses->end_use));
                } else {
                    return null;
                }
            })
            ->addColumn('update_status', function ($uses) {
                $dropdown = '<select class="item-status form-control" data-update-url="' . route('uses.updateStatus', $uses->id) . '">';
                $dropdown .= '<option value="in_use"';
                if ($uses->status === 'in_use') $dropdown .= ' selected';
                $dropdown .= '>Belum Selesai</option>';

                $dropdown .= '<option value="finish"';
                if ($uses->status === 'finish') $dropdown .= ' selected';
                $dropdown .= '>Selesai</option>';
                $dropdown .= '</select>';

                return $dropdown;
            })
            ->addColumn('actions', function ($uses) {
                $editBtn = '<button class="btn btn-warning mx-1 mb-1 edit-item-button" data-edit-url="' . route('uses.update', $uses->id) . '">
                    <i class="fas fa-edit mr-1"></i>
                    <span>Edit</span>
                </button>';
                $deleteBtn = '<button class="btn btn-danger mx-1 mb-1 delete-item-button" data-delete-url="' . route('uses.destroy', $uses->id) . '">
                    <i class="fas fa-trash mr-1"></i>
                    <span>Hapus</span>
                </button>';
                return $editBtn . $deleteBtn;
            })->rawColumns(['actions', 'update_status'])->make(true);
    }

    /**
     * Menyimpan data penggunaan barang baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'item_name' => 'required',
            'user_name' => 'required|max:255',
            'start_use' => 'required',
            'status' => 'required|in:in_use,finish',
        ]);

        $payload['end_use'] = null;

        if ($request->status === 'finish') {
            if ($request->end_use) {
                $payload['end_use'] = $request->end_use;
            } else {
                $payload['end_use'] = now();
            }
        }

        ItemUses::create($payload);

        return response()->json([
            'message' => 'Data berhasil ditambahkan',
        ], Response::HTTP_OK);
    }

    /**
     * Menampilkan data penggunaan barang berdasarkan id.
     *
     * @param  \App\Models\ItemUses  $itemUses
     * @return \Illuminate\Http\Response
     */
    public function show(ItemUses $itemUses)
    {
        $itemUses->start_use = date('Y-m-d\TH:i', strtotime($itemUses->start_use));
        if ($itemUses->end_use) $itemUses->end_use = date('Y-m-d\TH:i', strtotime($itemUses->end_use));

        return response()->json([
            'message' => 'Data penggunaan barang',
            'itemUses' => $itemUses
        ], Response::HTTP_OK);
    }

    /**
     * Mengupdate data penggunaan barang berdasarkan id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ItemUses  $itemUses
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ItemUses $itemUses)
    {
        $payload = $request->validate([
            'item_name' => 'required',
            'user_name' => 'required|max:255',
            'start_use' => 'required',
            'status' => 'required|in:in_use,finish',
        ]);

        $payload['end_use'] = $request->end_use ?? null;

        // Cek apakah status penggunaan berubah
        if ($itemUses->status !== $request->status) {
            // Cek jika status sebelumnya adalah sedang digunakan
            if ($itemUses->status === 'in_use' && !$request->end_use) {
                $payload['end_use'] = now();
            }
        }

        $itemUses->update($payload);

        return response()->json([
            'message' => 'Data berhasil diupdate'
        ], Response::HTTP_OK);
    }

    /**
     * Mengupdate status penggunaan barang.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ItemUses  $itemUses
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, ItemUses $itemUses)
    {
        $payload = $request->validate([
            'status' => 'required|in:in_use,finish',
        ]);

        $payload['end_use'] = $request->end_use ?? null;

        // Cek apakah status penggunaan berubah
        if ($itemUses->status !== $request->status) {
            if ($itemUses->status === 'in_use') {
                $payload['end_use'] = now();
            }
        }

        $itemUses->update($payload);

        return response()->json([
            'message' => 'Status berhasil diupdate',
            'end_use' => $itemUses->end_use ? date('Y/m/d h:i', strtotime($itemUses->end_use)) : null,
        ], Response::HTTP_OK);
    }

    /**
     * Menghapus data penggunaan barang di database berdasarkan id.
     *
     * @param  \App\Models\ItemUses  $itemUses
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemUses $itemUses)
    {
        if ($itemUses->delete()) {
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
        return (new ItemUsesExport)->download('Data-penggunaan-barang-' . date('d-m-Y') . '.xlsx');
    }

    /**
     * Export data barang sebagai file Pdf.
     *
     * @return \Barryvdh\DomPDF\Facade\Pdf
     */
    public function exportPDF()
    {
        $itemUses = ItemUses::all();

        $pdf = Pdf::loadView('item_uses.pdf', ['itemUses' => $itemUses]);
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

        Excel::import(new ItemUsesImport, $request->file('file_import'));

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
        return FacadesResponse::download(public_path() . "/templates/Import_penggunaan_barang_cleandry.xlsx");
    }
}
