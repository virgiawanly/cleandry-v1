@extends('layouts.main')

@section('content')
    <div class="row">
        @foreach ($outlets as $outlet)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="text-sm text-uppercase">Outlet</div>
                        <h3 class="mb-0">{{ $outlet->name }}</h3>
                    </div>
                    <div class="card-body">
                        <p>{{ $outlet->address }}</p>
                    </div>
                    <div class="card-header">
                        <form action="/select-outlet" method="POST">
                            @csrf
                            <button type="submit" name="outlet_id" value="{{ $outlet->id }}"
                                class="btn btn-primary float-right">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                <span>Masuk Outlet</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
