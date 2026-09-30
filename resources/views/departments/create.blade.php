@extends('layouts.app')
@section('page-title','Add Department')
@section('content')
<div class="card shadow-sm"><div class="card-body"><h3>Add Department</h3>
<form method="POST" action="{{ route('departments.store') }}">@csrf
<div class="row g-3"><div class="col-md-6"><label>Name</label><input name="name" class="form-control" required></div><div class="col-md-3"><label>Code</label><input name="code" class="form-control" required></div><div class="col-md-3"><label>Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div><div class="col-12"><label>Description</label><textarea name="description" class="form-control"></textarea></div></div><button class="btn btn-primary mt-3">Create</button></form>
</div></div>
@endsection
