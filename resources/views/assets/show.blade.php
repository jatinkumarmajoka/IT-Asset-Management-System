@extends('layouts.app')
@section('page-title','Asset Details')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
<div><h2>{{ $asset->item_name }}</h2><div class="text-muted">{{ $asset->asset_code }}</div></div>
<div><a href="{{ route('assets.edit',$asset) }}" class="btn btn-primary">Edit</a> <a href="{{ route('assets.index') }}" class="btn btn-light">Back</a></div>
</div>
<div class="row g-4">
<div class="col-lg-7"><div class="card shadow-sm"><div class="card-body">
<h5>Asset Information</h5>
<div class="row g-3 mt-1">
@foreach([
['Department',$asset->department->name],['Category',$asset->category->name],
['Make',$asset->make],['Model',$asset->model],['Serial Number',$asset->asset_serial_number],
['Barcode/Sticker',$asset->barcode_number],['Issued Quantity',$asset->issued_quantity],
['Physically Verified',$asset->physically_verified_quantity],['Occupant',$asset->occupant?->name ?? 'Unassigned'],
['Issue Date',$asset->date_of_issue?->format('d-m-Y') ?? '—'],['Status',$asset->asset_status],
['Condition',$asset->condition],['Location',$asset->location]
] as [$label,$value])
<div class="col-md-6"><div class="text-muted small">{{ $label }}</div><div class="fw-semibold">{{ $value ?: '—' }}</div></div>
@endforeach
</div>
</div></div></div>
<div class="col-lg-5"><div class="card shadow-sm"><div class="card-body">
<h5>History</h5>
@forelse($asset->history->sortByDesc('created_at') as $h)
<div class="border-bottom py-2"><strong>{{ ucfirst($h->action) }}</strong><br><small class="text-muted">{{ $h->created_at?->format('d-m-Y H:i') }} · {{ $h->ip_address }}</small></div>
@empty <div class="text-muted">No history yet.</div> @endforelse
</div></div></div>
</div>
@endsection
