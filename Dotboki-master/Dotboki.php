<?php

/**
 * @name Dotboki
 * @main Dotboki\Dotboki
 * @author ["#HashTag","NeosKR"]
 * @version 0.1
 * @api 4.0.0
 * @description This plugin is made by HashTag (NeosKR)
 */
namespace Dotboki;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\event\player\PlayerInteractEvent;

class Dotboki extends PluginBase implements Listener
{
	   
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

	public function msg ($player, $line1, $line2, $line3, $line4)
	{

		$line1 = "§r".$line1."§r";
		$line2 = "§r".$line2."§r";
		$line3 = "§r".$line3."§r";
		$line4 = "§r".$line4."§r";
		
		if (!isset($line1)) {
			$line1 = "§7아무 것도 쓰여있지 않습니다";
		}
		if (!isset($line2)) {
			$line2 = "§7아무 것도 쓰여있지 않습니다";
		}
		if (!isset($line3)) {
			$line3 = "§7아무 것도 쓰여있지 않습니다";
		}
		if (!isset($line4)) {
			$line4 = "§7아무 것도 쓰여있지 않습니다";
		}
		
		$player->sendMessage ("§6§l=====< §f표지판에 쓰여있는 글 §6>=====");
		$player->sendMessage ("§6§l[1번 줄] §r§f".$line1);
		$player->sendMessage ("§6§l[2번 줄] §r§f".$line2);
		$player->sendMessage ("§6§l[3번 줄] §r§f".$line3);
		$player->sendMessage ("§6§l[4번 줄] §r§f".$line4);
	}

    public function onTouch(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
		
        $id = $event->getItem()->getId();
        $damage = $event->getItem()->getDamage();

        $x = $event->getBlock()->getX();
        $y = $event->getBlock()->getY();
        $z = $event->getBlock()->getZ();
        $a = new Vector3($x, $y, $z);
        $tile = $event->getBlock()->getLevel()->getTile($a);
		
        if ($event->getBlock()->getId() == Block::SIGN_POST || $event->getBlock()->getId() == Block::WALL_SIGN) {
			if ($id == Item::STICK) {
				$this->msg ($player, $tile->getLine(0), $tile->getLine(1), $tile->getLine(2), $tile->getLine(3));
			}
		}
				
    }
	
}
?>