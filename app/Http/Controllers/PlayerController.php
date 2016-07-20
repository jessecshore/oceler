<?php

namespace oceler\Http\Controllers;

use Illuminate\Http\Request;
use oceler\Http\Requests;
use oceler\Http\Controllers\Controller;
use View;
use Auth;
use DB;
use Response;
use Session;
use oceler\Solution;
use oceler\SolutionCategory;

class PlayerController extends Controller
{
    public function home()
    {
      return redirect('player');
    }

    public function getShow()
    {
    	/**
    	* Let me take a moment to explain networks. The connections between users
    	*   are stored within a network of nodes. Each user is assigned a node.
    	*   Each node can serve as a source or a target to other nodes. A source is
    	*   a player whose information can be seen by the target. Connections can
    	*   be bi-directional (player A can see player B's information, and player B
    	*   can see Player A's information) or uni-directional (player A can see
    	*   player B's information, but player B CAN'T see player A's information).
    	*
    	* Below, we find the network relationships to the current user, and store
    	*   the players the user can see in one array ($players_from) and the players
    	*   that can see the user in another array ($players_to).
    	*/

    	// Get the user's ID, the ID of the network, and the user's network node
    	$u_id = Auth::id();
    	$network = DB::table('networks')
                      ->where('sess_id', '=', Auth::user()->session_id)
                      ->value('id');

    	$u_node = DB::table('user_nodes')
                    ->where('user_id', '=', $u_id)
                    ->value('node');

    	// Get each player that is in the same session as the user
    	$session_players = DB::table('users')
                              ->where('session_id', '=', Auth::user()->session_id)
                              ->get();

    	// Create two arrays -- one to hold the players the user can see, and another to hold the players that can see the user
    	$players_from = array();
    	$players_to = array();

    	// Then, loop through the players in the session
    	foreach ($session_players as $player) {

    		// Get the network node for this player
    		$node = DB::table('user_nodes')
                    ->where('user_id', '=', $player->id)
                    ->value('node');

    		// See if their node is a source where the user's node is a target
    		$from = DB::table('network_edges')
                    ->where('net_id', '=', $network)
                    ->where('source', '=', $node)
                    ->where('target', '=', $u_node)
                    ->value('source');

    		// See if their node is a target where the user's node is a source
    		$to = DB::table('network_edges')
                  ->where('net_id', '=', $network)
                  ->where('target', '=', $u_node)
                  ->where('source', '=', $node)
                  ->value('target');

    		// If they are a source (e.g. the user can see this player),
    		// add them to the $players_from array
    		if($from) $players_from[] = $player;

    		// If they are a target (e.g., this player can see the user),
    		// add them to the $players_to array
    		if($to) $players_to[] = $player;

    	}


    	/**
    	* Solution categories are stored in the DB. This makes it
    	*  possible to support different sessions
    	*  having different solution categories. At the moment,
    	*  all sessions use the same categories, so
    	*  we simply get them all in an array.
    	*/
    	$solution_categories = SolutionCategory::all();

    	Session::put('players_from', $players_from);
    	Session::put('players_to', $players_to);

    	// Finally, we generate the page, passing the user's id,
    	// the players_from and players_to arrays and the
    	// solution categories array
    	return View::make('layouts.player.main')
                   ->with('user', Auth::user())
                   ->with('players_from', $players_from)
                   ->with('players_to', $players_to)
                   ->with('solution_categories', $solution_categories);
    }

    /**
    * Stores a new solution to the solutions table in the DB
    *
    */
	public function  postSolution(Request $request)
	{

		$user = Auth::user();

		$sol = new Solution;
		$sol->category_id = $request->category_id;
		$sol->solution = $request->solution;
		$sol->confidence = $request->confidence;
		$sol->user_id = $user->id;

		$sol->save();
	}

    /**
    * Gets the most recent solutions for every player
    *  that is visible to the user
    *
    * @param solution_id	The id of the latest solution that was retrieved
    */
	public function getListenSolution($solution_id)
	{

		// We build an array of user IDs for each player
		//  the user can see (including themselves)
		//  to use in our query

		$ids[] = Auth::user()->id;

		foreach (Session::get('players_from') as $player) {
			$ids[] = $player->id;
		}

		// Get all solutions more recent than the last solution ID we have
		$solutions = DB::table('solutions')
                    ->whereIn('user_id', $ids)
                    ->where('id', '>', $solution_id)
                    ->get();

		return Response::json($solutions);

	}

    /**
    * This function ensures that users are authenticated (i.e. logged in)
    * before showing this page. If they are not, they are taken to a
    * login page.

    public function __construct()
    {
    	$this->middleware('auth');

    }
    */
}
