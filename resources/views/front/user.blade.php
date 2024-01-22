@extends('layouts.main')

@section('content')
<div>
   <user-select></user-select>
</div>
@endsection
@push('scripts')
<script>
    window.USERS = {!! json_encode($uniqueUserNames) !!};
</script>
@endpush

