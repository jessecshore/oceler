<?php

namespace oceler\Http\Controllers;

use Illuminate\Http\Request;
use oceler\Http\Requests;
use oceler\Http\Controllers\Controller;
use \oceler\Trial;
use View;
use Auth;
use DB;
use Response;
use Session;

class TrialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $trials = Trial::all();
      dump($trials);
      return View::make('layouts.admin.trials')
                  ->with('trials', $trials);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return View::make('layouts.admin.trial-config');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $trial = new Trial();
      $trial->distribution_interval = $request->distribution_interval;
      $trial->num_waves = $request->num_waves;
      $trial->num_players = $request->num_players;
      $trial->mult_factoid = $request->mult_factoid || 0;
      $trial->pay_correct = $request->pay_correct || 0;
      $trial->num_rounds = $request->num_rounds;
      $trial->is_active = false;

      $trial->save();

      /*
       * For each forund, the timeout factoidset, countryset, and
       * nameset are stored.
       */
      for($i = 0; $i < $trial->num_rounds; $i++){

        DB::table('trial_rounds')->insert([
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'trial_id' => $trial->id,
            'round' => ($i + 1),
            'round_timeout' => $request->round_timeout[$i],
            'factoidset_id' => $request->factoidset_id[$i],
            'countryset_id' => $request->countryset_id[$i],
            'nameset_id' => $request->nameset_id[$i],
            ]);
      }
      return Redirect::to('/admin/trial');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function toggle($id)
    {
      $trial = Trial::find($id);
      $trial->is_active = !$trial->is_active;
      $trial->save();
      return \Redirect::to('/admin/trial');
    }
}