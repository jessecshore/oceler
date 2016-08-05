@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('css/admin_style.css') }}">
@stop

@section('js')
  <script type="text/javascript" src="{{ asset('js/listen.js') }}"></script>
  <script>

    $(document).ready(function(){

      setInterval(function(){
        queueListener();
      }, 5000);

      playerTrialListener();

      /*
      setInterval(function(){
        playerTrialListener();
      }, 5000);
      */
    });

  </script>

@stop


@section('content')
    <div class="container">
      @include('layouts.admin.menu')
      <div class="row">
        <div class="col-md-12">
          <h3>[ This page will track in real time the users waiting to join a trial
            as well as the users that are in trials in progress.
            Currently, you will need to reload the page to see any changes.]</h3>
          <h1 class="text-center">Players</h1>
          <h2 class="text-primary">Players in trial queue</h2>
          <table id="queued_players" class="table table-striped trials">
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>IP Address</th>
              <th>User Agent</th>
              <th>Time Entered</th>
              <th>Last Pinged</th>
            </tr>
            <tbody class="players">
              @foreach($queued_players as $queue)
              <tr>
                <td>{{ $queue->users->name }}</td>
                <td>{{ $queue->users->email }}</td>
                <td>{{ $queue->users->ip_address }}</td>
                <td>{{ $queue->users->user_agent }}</td>
                <td>{{ $queue->created_at }}</td>
                <td>{{ $queue->updated_at }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <h2 class="text-primary">Players in active trials</h2>
            <table id="trials" class="table table-striped trials">
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>IP Address</th>
                <th>User Agent</th>
                <th>Time Entered</th>
                <th>Last Pinged</th>
                <th>Solutions</th>
              </tr>
              <tbody class="players">
                <tr><td colspan="5">[Needs to be added]</td></tr>
            </tbody>
            </table>
        </div>
      </div>
    </div>
@stop