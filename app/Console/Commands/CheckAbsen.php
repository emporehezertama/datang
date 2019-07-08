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
      $data = DB::table('checkinout')
                  ->select('checkinout.*','userinfo.badgenumber', 'temp_data.id as temp_data_id')
                  ->join('userinfo', 'userinfo.userid', '=', 'checkinout.userid') 
                  ->join('temp_data', 'temp_data.checkinout_id','=', 'checkinout.id')
                  ->where('temp_data.status', 0)
                  ->get();

      foreach($data as $item)
      {
        $data = [
              'absensi_number' => $item->badgenumber,
              'checktime' => $item->checktime,
              'sn' => $item->SN
            ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://em-hr.co.id/api/public/finger-store');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $content = curl_exec($ch);
        curl_close($ch);

        echo "\n";
        echo " Insert Absensi Number ". $item->badgenumber ." \n";
        echo $content ."\n";
        echo "\n";
        echo "\n";

        // update status
        DB::table('temp_data')
            ->where('id', $item->temp_data_id)
            ->update(['status' => 1]);
      }
   }
}