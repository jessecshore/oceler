<?php

namespace oceler;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class MTurk extends Model
{

  public static function testAwsSdk()
  {

    $client = new \Aws\MTurk\MTurkClient([
      'version' => 'latest',
      'region'  => env('AWS_REGION', ''),
      'endpoint' => env('AWS_ENDPOINT', '')
    ]);

    dump($client);
    return;

    //$result = $client->listHITs([
        //'MaxResults' => 100
    //]);

    $result = $client->createHIT([
    'AssignmentDurationInSeconds' => 5000, // REQUIRED
    'Description' => 'TESTING THE AWS SDK API', // REQUIRED
    'Keywords' => 'AWSSDKTEST, netlabexperiments',
    'LifetimeInSeconds' => 10000, // REQUIRED
    'MaxAssignments' => 5,
    'Question' => '
      <ExternalQuestion xmlns="http://mechanicalturk.amazonaws.com/AWSMechanicalTurkDataSchemas/2006-07-14/ExternalQuestion.xsd">
        <ExternalURL>https://netlabexperiments.org/MTurk-login</ExternalURL>
        <FrameHeight>600</FrameHeight>
      </ExternalQuestion>'
    ,
    'Reward' => '0.01', // REQUIRED
    'Title' => 'TEST QUESTION', // REQUIRED
    'UniqueRequestToken' => '1234567890',
]);

    dump($result);
//https://requestersandbox.mturk.com/mturk/manageHIT?viewableEditPane=manageHIT_expire&HITId=341H3G5YFZDBOAZWUCWEVQKBIPOZ0N
/*
https://tictactoe.amazon.com/gamesurvey.cgi?gameid=01523
&assignmentId=123RVWYBAZW00EXAMPLE456RVWYBAZW00EXAMPLE
&hitId=123RVWYBAZW00EXAMPLE
&turkSubmitTo=https://www.mturk.com/
&workerId=AZ3456EXAMPLE
*/
  }

  public static function postHitData($assignment_id, $mturk_id, $total_earnings,
                                     $passed_trial, $completed_trial)
  {
    echo $mturk_id;

    $mturk_hit = \DB::table('mturk_hits')
                    ->where('assignment_id', '=', $assignment_id)
                    ->where('worker_id', '=', $mturk_id)
                    ->first();

    $client = new \Aws\MTurk\MTurkClient([
      'version' => 'latest',
      'region'  => env('AWS_REGION', ''),
      'endpoint' => $mturk_hit->submit_to
    ]);

    if($completed_trial){
      $result = $client->approveAssignment([
        'AssignmentId' => $assignment_id,
        'RequesterFeedback' => 'Great job!',
      ]);
    }

    if($total_earnings['bonus'] != 0){
      $result = $client->sendBonus([
        'AssignmentId' => $assignment_id,
        'BonusAmount' => $total_earnings['bonus'],
        'Reason' => $total_earnings['bonus_reason'],
        'UniqueRequestToken' => $mturk_hit->unique_token,
        'WorkerId' => $mturk_id,
      ]);
    }

    if($passed_trial){
      $last_trial_type = DB::table('trial_user_archive')
                            ->where('user_id', '=', Auth::id())
                            ->orderBy('trial_type', 'DESC')
                            ->take(1)
                            ->pluck('trial_type');

      $result = $client->associateQualificationWithWorker([
        'IntegerValue' => $last_trial_type + 1,
        'QualificationTypeId' => "3DDNYIPUQNTSBR52F1XBRX6XW33RZA",
        'SendNotification' => false,
        'WorkerId' => $mturk_id,
      ]);
    }

    $client = new \GuzzleHttp\Client();
    $response = $client->request('POST',
                                 $mturk_hit->submit_to.'/mturk/externalSubmit',
                                 [
                                   'form_params' => [
                                                      'assignmentId' => $assignment_id
                                                    ]
                                                  ]);
  }
}
