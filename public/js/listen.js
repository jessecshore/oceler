var last_solution = 0;
var last_message_time = 0;

function solutionListener()
{

	$.ajax({
		type: "GET",
		url: "/listen/solution/"+last_solution,
		success: function(solutions)
		{

			if(solutions == -1){
				window.location = '/player/trial/end';
			}

			$.each(solutions, function(key, sol)
			{
				addNewSolution(sol);
				last_solution = sol.id;
			});

		}
	});

	setTimeout(solutionListener, 2000);

}

function messageListener()
{
	setTimeout(messageListener, 2000);

	// If the player is typing a reply, just return
	if($(".reply-form").is(":visible")) return;

	$.ajax({
		type: "GET",
		url: "/listen/message/"+last_message_time,
		success: function(messages)
		{
			console.log(messages);
			$.each(messages, function(key, msg)
			{

				var m = new Message(msg.users, msg.sender, msg.message, msg.factoid, msg.share_id, msg.id);
				console.log(msg.users);
				m.addMessage($("#messages"));

				$.each(msg.replies, function(key, reply){
					var r = new Reply(msg.users, reply.replier, reply.message, msg.id);
					r.addMessage($("#msg_" + msg.id));
				});

				if(msg.shared_from){
					var shared = new Reply(msg.shared_from.users, msg.shared_from.sender,
																	msg.shared_from.message, msg.shared_from.factoid,
																	msg.shared_from.share_id, msg.shared_from.id);
					shared.addMessage($("#msg_" + msg.id));
				}

				last_message_time = msg.updated_at;
			});


		}
	});
}

function queueListener()
{

  $.ajax({
    type: "GET",
    url: "/admin/listen/queue/",
    success: function(queued_players)
    {

      $("#queued_players>tbody.players").html('');

      $.each(queued_players, function(key, queue){

        var row = $('<tr>');
        var name = $('<td>' + queue.users.name + '</td>');
        var email = $('<td>' + queue.users.email + '</td>');
        var ip = $('<td>' + queue.users.ip_address + '</td>');
        var user_agent = $('<td>' + queue.users.user_agent + '</td>');
        var created = $('<td>' + queue.created_at  + '</td>');
        var updated = ('<td>' + queue.updated_at + '</td>');

        $(row).append(name, email, ip, user_agent, created, updated);
        $("#queued_players>tbody.players").append(row);

      });

    }
  });
}

function playerTrialListener(trial_id)
{

  $.ajax({
    type: "GET",
    url: url,
    success: function(trial_players)
    {


      $("#trials>tbody.players").html('');

      $.each(trial_players, function(i, trial){

				$.each(trial.users, function(j, user){

	        var row = $('<tr>');
	        var name = $('<td>' + user.player_name + '</td>');
	        var email = $('<td>' + user.email + '</td>');
	        var ip = $('<td>' + user.ip_address + '</td>');
	        var user_agent = $('<td>' + user.user_agent + '</td>');
	        var created = $('<td>' + user.pivot.created_at  + '</td>');
	        var updated = $('<td>' + user.updated_at + '</td>');

					var solution_table = '<table>';

					$.each(user.solutions, function(k, sol){
						solution_table += '<tr>';
						solution_table += '<td>' + sol.name + ':</td>';
						solution_table += '<td>' + sol.confidence + '%</td>';
						solution_table += '<td>' + sol.solution + '</td>';
						solution_table += '</tr>';
					});

					solution_table += '</table>';

					var solutions = $('<td>' + solution_table + '</td>');

	        $(row).append(name, email, ip, user_agent, created, updated, solutions);

	        $("#trials>tbody.players").append(row);
				});
      });

    }
  });
}

function distributionListener(node, wave, distribution_interval, factoidset_id)
{
	// Increment wave by one
	wave++;

  console.log("node" + node + " wave" + wave + " factoidset_id" + factoidset_id);
	var delay = distribution_interval * 60000; // Converted from minutes to milliseconds
	$.ajax({
		type: "GET",
		url: "/listen/system-message/",
		data: {"node" : node, "wave" : wave, "factoidset_id" : factoidset_id}
	});

	setTimeout(function(){
		distributionListener(node, wave, distribution_interval, factoidset_id);
	}, delay)

}
