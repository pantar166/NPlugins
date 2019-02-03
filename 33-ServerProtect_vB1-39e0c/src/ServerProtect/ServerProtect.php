<?php

namespace ServerProtect;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\event\player\PlayerInteractEvent;

use pocketmine\block\Block;
use pocketmine\item\Item;

use pocketmine\Server;
use pocketmine\Player;

use pocketmine\utils\Config;

class ServerProtect extends PluginBase implements Listener{

    public function onEnable() {
	    $this->getServer()->getPluginManager()->registerEvents($this, $this);
		@mkdir ( $this->getDataFolder () );
		$this->database = (new Config ( $this->getDataFolder () . "setting.yml", Config::YAML,
		[
		"전송할 메시지" => "§e§l[ §f시스템 §e] §r§f서버를 수정할 수 없습니다",
		"world" => "true"
		]));
        $this->db = $this->database->getAll ();
    }
  
    public function onTouch(PlayerInteractEvent $event){
        $player = $event->getPlayer();
		
        $b = $event->getBlock();
		$bi = $b->getId();
		$bd = $b->getDamage();
		
		$i = $player->getInventory()->getItemInHand();
		$ii = $i->getId();
		$id = $i->getDamage();
		
		if ( ! $player->isOp() ) {
	    if ( isset ( $this->db [$b->getLevel()->getFolderName()] ) ) {
        if ( $bi == 2 or $bi == 3 ) {
        if ( $ii == 256 or $ii == 269 or $ii == 273 or $ii == 277 or $ii == 284 or $ii == 290 or $ii == 291 or $ii == 292 or $ii == 293 or $ii == 294 ) {
            $event->setCancelled(true);
			$player->sendMessage ($this->db ["전송할 메시지"]);
        }
        }
		}
		}
    }
}