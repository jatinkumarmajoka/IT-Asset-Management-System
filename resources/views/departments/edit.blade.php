@extends('layouts.app')
@section('page-title','Edit Department')
@section('content')
<div class="card shadow-sm"><div class="card-body"><h3>Edit Department</h3>
<form method="POST" action="{{ route('departments.update',$department) }}">@csrf @method('PUT')
<div class="row g-3"><div class="col-md-6"><label>Name</label><input name="name" class="form-control" value="{{ $department->name }}" required></div><div class="col-md-3"><label>Code</label><input name="code" class="form-control" value="{{ $department->code }}" required></div><div class="col-md-3"><label>Status</label><select name="status" class="form-select"><option value="active" @selected($department->status=='active')>Active</option><option value="inactive" @selected($department->status=='inactive')>Inactive</option></select></div><div class="col-12"><label>Description</label><textarea name="description" class="form-control">{{ $department->description }}</textarea></div></div><button class="btn btn-primary mt-3">Save</button></form>
</div></div>
@endsection
