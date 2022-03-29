<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;

class ServiceTypeController extends Controller
{
    /**
     * Menampilkan halaman manajemen jenis layanan.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('service-types.index', [
            'title' => 'Kelola Jenis Cucian',
            'breadcrumbs' => [
                [
                    'href' => '/service-types',
                    'label' => 'Jenis Cucian'
                ]
            ],
        ]);
    }

    /**
     * Mendapatkan data jenis layanan untuk datatable.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable()
    {
        $types = ServiceType::all();

        return DataTables::of($types)
            ->addIndexColumn()
            ->addColumn('actions', function ($type) {
                $editBtn = '<button data-update-url="' . route('service-types.update', $type->id) . '" class="btn btn-warning mx-1 mb-1 update-button">
                    <i class="fas fa-edit mr-1"></i>
                    <span>Edit</span>
                </button>';
                $deleteBtn = '<button data-delete-url="' . route('service-types.destroy', $type->id) . '" class="btn btn-danger mx-1 mb-1 delete-button">
                    <i class="fas fa-trash mr-1"></i>
                    <span>Hapus</span>
                </button>';
                return $editBtn . $deleteBtn;
            })->rawColumns(['actions'])->make(true);
    }

    /**
     * Menyimpan data jenis layanan baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        ServiceType::create([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Data berhasil ditambahkan',
        ], Response::HTTP_OK);
    }

    /**
     * Mendapatkan data jenis layanan berdasarkan id tertentu.
     *
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceType $serviceType)
    {
        return response()->json([
            'message' => 'Data jenis cucian',
            'service_type' => $serviceType
        ], Response::HTTP_OK);
    }

    /**
     * Mengupdate data jenis layanan berdasarkan id tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ServiceType $serviceType)
    {
        $request->validate([
            'name' => 'required|unique:service_types,name,' . $serviceType->id,
        ]);

        $serviceType->update([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Data berhasil diupdate',
        ], Response::HTTP_OK);
    }

    /**
     * Menghapus data jenis layanan berdasarkan id tertentu.
     *
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceType $serviceType)
    {
        if ($serviceType->delete()) {
            return response()->json([
                'message' => 'Data berhasil dihapus'
            ], Response::HTTP_OK);
        };

        return response()->json([
            'message' => 'Terjadi kesalahan'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
