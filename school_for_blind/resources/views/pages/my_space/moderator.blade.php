@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0" style="color: var(--text-main);">مساحتي</h3>
  </div>

  @include('pages.my_space.partials.notes')
  @include('pages.my_space.partials.profile_info')

</div>
@endsection