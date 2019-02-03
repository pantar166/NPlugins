<?php

namespace CooltimeBox;

use pocketmine\plugin\PluginBase; 
use pocketmine\event\Listener; 
use pocketmine\block\Block;
use pocketmine\item\Item; 
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\player\PlayerInteractEvent;

class CooltimeBox extends PluginBase implements Listener {

private $cool = [];
	
public function onEnable() {
$this->getServer()->getPluginManager()->registerEvents ( $this, $this );
}

public function makeTimestamp() {
	$date = date ( "Y-m-d H:i:s" );
	$yy = substr ( $date, 0, 4 );
	$mm = substr ( $date, 5, 2 );
	$dd = substr ( $date, 8, 2 );
	$hh = substr ( $date, 11, 2 );
	$ii = substr ( $date, 14, 2 );
	$ss = substr ( $date, 17, 2 );
	return mktime ( $hh, $ii, $ss, $mm, $dd, $yy );
}

public function ChestOpen(PlayerInteractEvent $event){
    $player = $event->getPlayer();
    $name = $player->getName();
    if ( $event->getBlock()->getId() == 54 ) {
    if ( isset ( $this->cool [$name] )) {
  	if ( $this->makeTimestamp () - $this->cool [$name] < 30 ) {
  		$cool = 30 - ( $this->makeTimestamp() - $this->cool [$name] );
  		$event->setCancelled();
  		$player->sendMessage ("§b§l[§f상자약탈§b] §r§f상자를 열기까지 §b" . $cool . "초 §f남았습니다.");
  		return true;
  	    }
    }   
    $this->cool [$name] = $this->makeTimestamp();
    $player->sendMessage ("§b§l[§f상자약탈§b] §r§f상자를 열었습니다!");
    }
    }
}