<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Return data for DataTables.
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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
}
