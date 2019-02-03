<?php
namespace NeosDobak;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use onebone\economyapi\EconomyAPI;
class NeosDobak extends PluginBase implements Listener{
public function onEnable(){
$this->getServer ()->getPluginManager ()->registerEvents ( $this, $this );
}
public function PlayerInteract(PlayerInteractEvent $event) {
$player = $event->getPlayer ();
$inventory = $player->getInventory (); 	
$name = $player->getName();
if($event-> getBlock ()->getId () == 165 && $event->getBlock ()->getDamage () == 0){
if(!$event->getPlayer()->getInventory()->contains(Item::get(382, 0, 1))) {
$event->getPlayer()->sendTitle("§c§l[Error]", "§e§l[ §f도박 §e] §r§f당신은 도박코인을 가지고 있지 않습니다 ( 도박코인 : 빛나는 수박 )");
return true;
}else {
$event->getPlayer()->getInventory()->removeItem(new Item(382, 0, 1));
switch(mt_rand(0,8)){
case 0:
$this->getServer()->broadcastMessage("§b§l===================================\n§e§l[ §f도박 §e] §r§f{$name}님이 도박으로 만원을 얻으셨습니다.\n§b§l===================================");
EconomyAPI::getInstance()->addMoney ( $player, 10000 );
break;
case 1:
$this->getServer()->broadcastMessage("§b§l===================================\n§e§l[ §f도박 §e] §r§f{$name}님이 도박으로 십만원을 얻으셨습니다.\n§b§l===================================");
EconomyAPI::getInstance()->addMoney ( $player, 30000 );
break;
case 2:
$this->getServer()->broadcastMessage("§b§l===================================\n§e§l[ §f도박 §e] §r§f{$name}님이 도박으로 오만원을 얻으셨습니다.\n§b§l===================================");
EconomyAPI::getInstance()->addMoney ( $player, 50000 );
break;
case 3:
$this->getServer()->broadcastMessage("§b§l===================================\n§e§l[ §f도박 §e] §r§f{$name}님이 도박으로 아무것도 얻지 못하셨습니다.\n§b§l===================================");
break;
case 4:
$this->getServer()->broadcastMessage("§b§l===================================\n§e§l[ §f도박 §e] §r§f{$name}님이 도박으로 오천원을 얻으셨습니다.\n§b§l===================================");
EconomyAPI::getInstance()->addMoney ( $player, 5000 );
break;
case 5:
$this->getServer()->broadcastMessage("§b§l===================================\n§e§l[ §f도박 §e] §r§f{$name}님이 도박으로 천원을 얻으셨습니다.\n§b§l===================================");
EconomyAPI::getInstance()->addMoney ( $player, 1000 );
break;
case 6;
$this->getServer()->broadcastMessage("§b§l===================================\n§e§l[ §f도박 §e] §r§f{$name}님이 도박으로 이십만원을 얻으셨습니다.\n§b§l===================================");
break;
case 7;
$this->getServer()->broadcastMessage("§b§l===================================\n§e§l[ §f도박 §e] §r§f{$name}님이 도박으로 아무것도 얻지 못하셨습니다.\n§b§l===================================");
break;
case 8;
$this->getServer()->broadcastMessage("§b§l===================================\n§e§l[ §f도박 §e] §r§f{$name}님이 도박으로 아무것도 얻지 못하셨습니다.\n§b§l===================================");
break;
case 8;
$this->getServer()->broadcastMessage("§b§l===================================\n§e§l[ §f도박 §e] §r§f{$name}님이 도박으로 아무것도 얻지 못하셨습니다.\n§b§l===================================");
break;
case 9;
$this->getServer()->broadcastMessage("§b§l===================================\n§e§l[ §f도박 §e] §r§f{$name}님이 도박으로 아무것도 얻지 못하셨습니다.\n§b§l===================================");
break;
case 10:
$this->getServer()->broadcastMessage("§b§l===================================\n§e§l[ §f도박 §e] §r§f{$name}님이 도박으로 아무것도 얻지 못하셨습니다.\n§b§l===================================");
break;
case 11:
$this->getServer()->broadcastMessage("§b§l===================================\n§e§l[ §f도박 §e] §r§f{$name}님이 도박으로 아무것도 얻지 못하셨습니다.\n§b§l===================================");
break;
case 12:
$this->getServer()->broadcastMessage("§b§l===================================\n§e§l[ §f도박 §e] §r§f{$name}님이 도박으로 아무것도 얻지 못하셨습니다.\n§b§l===================================");
break;
}
}
}
}
}