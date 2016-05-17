<?php

/*
*     _    _ _        ___  ______ _____
*    | |  | (_)      / _ \ | ___ \_   _|
*    | |  | |_ _ __ / /_\ \| |_/ / | |
*    | |/\| | | '_ \|  _  ||  __/  | |
*    \  /\  / | | | | | | || |    _| |_
*     \/  \/|_|_| |_\_| |_/\_|    \___/
*
* An easy to use API which counts and saves wins for each player.
* by BoxOfDevs
*/

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
          copy("http://files.himbeer.me/plugins/winapi-compatible.yml", $this->getDataFolder() . "plugins.yml");
          $this->plugins = new Config($this->getDataFolder(). "plugins.yml", Config::YAML);
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
     public function setWins($player, $wins){
          if($player instanceof Player){
               $player = $player->getName();
          }
          $player = strtolower($player);
          if(!isset($this->getwindata["wins"][$player])){
               return false;
          }else{
               $currentwins = $this->getWins($player);
               $this->getwindata["wins"][$plrname] = $wins;
               return true;
          }
     }
     public function saveData(){
          $this->data->setAll($this->getwindata);
          $this->data->save();
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
     public function onQuit(PlayerQuitEvent $qevent){
          $this->saveData();
     }
     public function onCommand(CommandSender $sender, Command $command, $label, array $args){
          switch($command->getName()){
               case "winapi":
                    if(!isset($args[0])){
                         $sender->sendMessage("Usage: /winapi info|plugins");
                         break;
                    }else{
                         switch($args[0]){
                              case "info":
                                   $sender->sendMessage("You are using WinAPI by BoxOfDevs V 1.0.0!");
                              case "plugins":
                                   $useplugins = $this->plugins->get("useplugins");
                                   $viewplugins = $this->plugins->get("viewplugins");
                                   $sender->sendMessage("Compatible plugins:\nPlugins wich use the WinAPI to save wins:" . $useplugins . "\nPlugins wich can show wins (stats):" . $viewplugins . "\nFor more info go on boxofdevs.ml");
                         }
                         break;
                    }
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
               case "getwins":
                    if(!isset($args[0])){
                         $sender->sendMessage("Usage: /getwins <player>");
                         break;
                    }else{
                         $player = $this->getServer()->getPlayer($args[0]);
                         if($player instanceof Player){
                              $wins = $this->getWins($player);
                              $message = $this->getMsg("getwins", "{wins}", $wins);
                              $plrname = $player->getName();
                              $message = str_replace("{player}", $plrname);
                              $sender->sendMessage($message);
                              break;
                         }else{
                              $message = $this->getMsg("notfound", "{name}", $args[0]);
                              $sender->sendMessage($message);
                              break;
                         }
                    }
               case "addwin":
                    if(!isset($args[0])){
                         $sender->sendMessage("Usage: /addwin <player>");
                         break;
                    }else{
                         $player = $this->getServer()->getPlayer($args[0]);
                         if($player instanceof Player){
                              $this->addWin($player);
                              $plrname = $player->getName();
                              $message = $this->getMsg("addwin", "{player}", $plrname);
                              $sender->sendMessage($message);
                              break;
                         }else{
                              $message = $this->getMsg("notfound", "{name}", $args[0]);
                              $sender->sendMessage($message);
                              break;
                         }
                    }
               case "addwins":
                    if(!isset($args[0]) || !isset($args[1])){
                         $sender->sendMessage("Usage: /addwins <player> <wins>");
                         break;
                    }else{
                         if(!is_numeric($args[1])){
                              $sender->sendMessage("Usage: /addwins <player> <wins>");
                              break;
                         }else{
                              $player = $this->getServer()->getPlayer($args[0]);
                              if($player instanceof Player){
                                   $this->addWins($player, $args[1]);
                                   $plrname = $player->getName();
                                   $message = $this->getMsg("addwins", "{player}", $plrname);
                                   $sender->sendMessage($message);
                                   break;
                              }else{
                                   $message = $this->getMsg("notfound", "{name}", $args[0]);
                                   $sender->sendMessage($message);
                                   break;
                              }
                         }
                    }
               case "setwins":
                    if(!isset($args[0]) || !isset($args[1])){
                         $sender->sendMessage("Usage: /setwins <player> <wins>");
                         break;
                    }else{
                         if(!is_numeric($args[1])){
                              $sender->sendMessage("Usage: /setwins <player> <wins>");
                              break;
                         }else{
                              $player = $this->getServer()->getPlayer($args[0]);
                              if($player instanceof Player){
                                   $this->setWins($player, $args[1]);
                                   $plrname = $player->getName();
                                   $message = $this->getMsg("setwins", "{player}", $plrname);
                                   $sender->sendMessage($message);
                                   break;
                              }else{
                                   $message = $this->getMsg("notfound", "{name}", $args[0]);
                                   $sender->sendMessage($message);
                                   break;
                              }
                         }
                    }
          return true;
          }
     }
     public function onDisable(){
          $this->saveData();
          $this->getLogger->info("Data saved and disabled!");
     }
}
