@extends('layouts.result')

@section('content')
<mailresult :tests="{{ json_encode($tests) }}" :datas="{{ json_encode($data) }}"></mailresult>
@endsection
