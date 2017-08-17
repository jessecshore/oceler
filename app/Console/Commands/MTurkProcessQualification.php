<?php

namespace oceler\Console\Commands;
use Illuminate\Console\Command;

class MTurkProcessQualification extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MTurkProcessQualification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates HIT qualifications using the MTurk API';

    private $hits;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->hits = \oceler\MturkHit::where('qualification_processed', '=', 0)
                                      ->get();
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $mturks = [];
        foreach ($this->hits as $key => $hit) {
          $mturks[$key] = new \oceler\MTurk\MTurk();
          $mturks[$key]->hit = $hit;
          $mturks[$key]->process_qualification();
        }
    }
}