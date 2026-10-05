@extends('layouts.app')
@section('page-title','Edit Asset')
@section('content')
<div class="card shadow-sm table-card">
    <div class="card-body">
        <h3 class="mb-4">Edit Asset — {{ $asset->asset_code }}</h3>
        <form method="POST" action="{{ route('assets.update',$asset) }}">
            @method('PUT')
            @include('assets._form', ['buttonText'=>'Save Changes'])
        </form>
    </div>
</div>
@endsection