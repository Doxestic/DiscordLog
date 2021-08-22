<?php


namespace Doxestic\DiscordLog;

use pocketmine\scheduler\Task;

class sendTask extends Task
{

    public Main $main;
    public $try = 5;

    public function __construct(Main $pl)
    {
        $this->main = $pl;
    }

    public function onRun(int $currentTick)
    {
        $main = $this->main;
        $main->getLogger()->info("trying to send Log...");
        $r = $this->sendMessage($main->logs);
        if ($r == ""){
            $main->getLogger()->info("send Logs " . $r);
            $main->logs = "";
            return;
        }
        if ($r != false){
            $err = json_decode($r, true);
            if (isset($err["message"])){
                $main->getLogger()->error($err["message"]);
            }
            if ($this->try < 0){
                $this->onRun($currentTick);
                $this->try--;
                $main->getLogger()->info("cant send Message will try again later Trys remain: " . $this->try);
            }else{
                $this->try = 5;
            }
        }
        // TODO: Implement onRun() method.
    }

    public function sendMessage($msg){
        $a = [
            "content" => "$msg",
            "username" => "Survival Land Log",
        ];
        $hookObject = json_encode($a, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

        $headers = [ 'Content-Type: application/json; charset=utf-8' ];
        $POST = [ 'username' => 'Testing BOT', 'content' => 'Testing message' ];

        $ch = curl_init($this->main->url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 3000);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $hookObject);
        $response   = curl_exec($ch);
        return $response;
    }
}