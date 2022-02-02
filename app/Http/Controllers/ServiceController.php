<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
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
        return view('services.index', [
            'title' => 'Kelola Layanan',
            'breadcrumbs' => [
                [
                    'href' => '/services',
                    'label' => 'Layanan'
                ]
            ],
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
            ->addColumn('actions', function() {
                $editBtn = '<button class="btn btn-info mx-1">
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        //
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
        //
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
