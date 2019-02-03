<?php

namespace NCheck;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\Player;
     
class NCheck extends PluginBase implements Listener {

	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args):bool{
		if ($command->getName() == "조회") {
			if (!isset ($args[0])) {
				$sender->sendMessage ("§d§l[ §f시스템 §d] §r§f/조회 (닉네임) | 해당 유저가 접속한 적이 있는지 확인합니다");
			} else {
				$a = strtolower ($args[0]);
				if(file_exists (Server::getInstance()->getDataPath()."players/"."{$a}.dat")){
					$sender->sendMessage ("§d§l[ §f시스템 §d] §r§d{$a} §f님은 서버에 접속하신 적이 §d§l있습니다§r");
				} else {
					$sender->sendMessage ("§d§l[ §f시스템 §d] §r§d{$a} §f님은 서버에 접속하신 적이 §d§l없습니다§r");
				}
			}
		}
		return true;
	}
}
