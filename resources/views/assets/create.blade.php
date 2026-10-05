@extends('layouts.app')
@section('page-title','Add Asset')
@section('content')
<div class="card shadow-sm table-card">
    <div class="card-body">
        <h3 class="mb-4">Add Asset</h3>
        <form method="POST" action="{{ route('assets.store') }}">
            @include('assets._form', ['buttonText'=>'Create Asset'])
        </form>
    </div>
</div>
@endsection 