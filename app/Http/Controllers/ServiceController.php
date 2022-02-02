<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
            'types' => $types
        ]);
    }

    /**
     * Datatable
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable()
    {
        $services = Service::with('type', 'outlet')->where('outlet_id', Auth::user()->outlet_id)->get();

        return DataTables::of($services)
            ->addIndexColumn()
            ->addColumn('actions', function($service) {
                $editBtn = '<button onclick="editHandler(' . "'" . route('services.update', $service->id) . "'" . ')" class="btn btn-info mx-1">
                    <i class="fas fa-edit"></i>
                    <span>Edit layanan</span>
                </button>';
                $deletBtn = '<button class="btn btn-danger mx-1">
                    <i class="fas fa-trash"></i>
                    <span>Hapus layanan</span>
                </button>';
                return $editBtn . $deletBtn;
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

        Auth::user()->outlet->services()->create($payload);

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil ditambahkan'
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data layanan',
            'service' => $service
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
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
            'success' => true,
            'message' => 'Layanan berhasil ditambahkan'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        //
    }
}
