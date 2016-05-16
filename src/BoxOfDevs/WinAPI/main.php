<?php
namespace BoxOfDevs\WinAPI;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\Player;
class Main extends PluginBase implements Listener{
     
     public function onEnable(){
          $this->getServer()->getPluginManager()->registerEvents($this,$this);
          $this->getLogger()->info("WinAPI by BoxOfDevs enabled!");
          $this->saveResource("config.yml");
          $this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML);
          $this->data = new Config($this->getDataFolder(). "data.yml", Config::YAML, ["wins" => []]);
          $this->getwindata = $this->data->getAll();
     }
     public function getMsg(string $msg, string $to_replace, $replace_with) : string{
          $output = $this->config->get($msg);
          $output = str_replace($to_replace, $replace_with, $output);
          return $output;
     }
     public function getWins($player){
          if($player instanceof Player){
               $player = $player->getName();
          }
          $player = strtolower($player);
          if(!isset($this->getwindata["wins"][$player])){
               return false;
          }else{
               return $this->getwindata["wins"][$player];
          }
     }
     public function addWins($player, $wins){
          if($player instanceof Player){
               $player = $player->getName();
          }
          $player = strtolower($player);
          if(!isset($this->getwindata["wins"][$player])){
               return false;
          }else{
               $currentwins = $this->getWins($player);
               $this->getwindata["wins"][$plrname] = $currentwins + $wins;
               return true;
          }
     }
     public function addWin($player){
          $this->addWins($player, 1);
          return true;
     }
     public function onJoin(PlayerJoinEvent $event){
          $player = $event->getPlayer();
          $plrname = strtolower($player->getName());
          if(!isset($this->getwindata["wins"][$plrname])){
               $this->getwindata["wins"][$plrname] = 0;
               return true;
          }else{
               return false;
          }
     }
     public function onCommand(CommandSender $sender, Command $command, $label, array $args){
          switch($command->getName()){
               case "mywins":
                    if($sender instanceof Player){
                         $wins = $this->getWins($sender);
                         $message = $this->getMsg("mywins", "{wins}", $wins);
                         $sender->sendMessage($message);
                         break;
                    }else{
                         $sender->sendMessage("Please use this command ingame!");
                         break;
                    }
          }
          return true;
     }
}
