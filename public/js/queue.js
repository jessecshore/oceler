function queue()
{
  $.ajax({
    type: "GET",
    url: "/player/trial/queue/status",
    success: function(status)
    {
      if(status == -1){
        leaveQueue();
        console.log("NO TRIAL");
      }

      if(status == 0){
        window.location.replace("/player/trial/instructions");
      }
    }
  });

  // If they are still waiting after 5 mins, leave
  setTimeout(leaveQueue, 300000);
  setTimeout(queue, 2000);
}

function waitForInstructions(trial_id)
{

  $.ajax({
    type: "GET",
    url: "/player/trial/instructions/status/" + trial_id,
    success: function(status)
    {

      response = $.parseJSON(status);
      if(response){
        document.cookie = 'generic_timer=; Max-Age=-99999999;';
        window.location.replace("/player/trial/initialize");
      }
      else {
        console.log(response);
      }

      setTimeout(function(){
        waitForInstructions(trial_id);
      }, 1000);
    }
  });

  //setTimeout(leaveQueue, 120000);
}

function markAsRead(user_id)
{

  $.ajax({
    type: "GET",
    url: "/player/trial/instructions/status/read/" + user_id,
    success: function(status)
    {
      return;
    }
  });

}

function leaveQueue()
{
  window.location.replace("/player/end-task/timeout");
}
