@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('css/admin_style.css') }}">
@stop

@section('content')
    <div class="container">
      @include('layouts.admin.menu')
      <div class="row">
        <div class="col-md-12">
          <h1 class="text-center">Log files</h1>

          @foreach($logs as $log)
            <h3>
              {{$log['name']}} :: <span class="text-muted">{{ $log['date'] }}</span>
              <a href="/admin/log/{{$log['id']}}">
                 view
              </a>

              <a href="/admin/log/download/{{$log['id']}}">
                download
              </a>
            </h3>

          @endforeach
        </div>
      </div>
@stop
