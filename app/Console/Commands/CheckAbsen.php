<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckAbsen extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'absen:check';
   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Command description';
   /**
    * Create a new command instance.
    *
    * @return void
    */
   public function __construct()
   {
       parent::__construct();
   }
   /**
    * Execute the console command.
    *
    * @return mixed
    */
   public function handle()
   {  
      $item = DB::table('checkinout')
                  ->select('checkinout.*','userinfo.badgenumber')
                  ->join('userinfo', 'userinfo.userid', '=', 'checkinout.userid') 
                  ->first();
      $data = [
            'absensi_number' => $item->badgenumber,
            'checktime' => $item->checktime
          ];

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'http://em-hr.co.id/api/public/finger-store');
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); // Cookie aware
      curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie); // Cookie aware
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

      $content = curl_exec($ch);
      curl_close($ch);

      return $content;
   }
}