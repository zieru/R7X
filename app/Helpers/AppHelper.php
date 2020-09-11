<?php
namespace App\Helpers;
class AppHelper
{
    public static function sendErrorAndExit($msg,$code = 500)
    {
        header('Access-Control-Allow-Origin: \'http://10.23.4.133:7000\',\'http://localhost:8000\',\'http://10.23.4.133:8000\'');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header("Access-Control-Allow-Headers: X-Requested-With");
        http_response_code($code);
        exit(json_encode(array('message'=> $msg)));

    }

    public function startQueryLog()
    {
        \DB::enableQueryLog();
    }

    public function showQueries()
    {
        dd(\DB::getQueryLog());
    }

    public static function instance()
    {
        return new AppHelper();
    }
}
