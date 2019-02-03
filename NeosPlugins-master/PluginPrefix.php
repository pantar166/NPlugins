<?php

/**
 * @name PluginPrefix
 * @main PluginPrefix\PluginPrefix
 * @author ["#HashTag","NeosKR"]
 * @version 0.1
 * @api 4.0.0
 * @description This plugin is for NeosKR's plugins
 */
namespace PluginPrefix;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;

class PluginPrefix extends PluginBase implements Listener
{

    public function onEnable()
    {
        $this->getServer()
            ->getPluginManager()
            ->registerEvents($this, $this);
    }

    public function msg($player, $p = "시스템", $a)
    {
        $player->sendMessage("§r§c§l《§f {$p} §r§c§l》§r §f".$a);
    }
	
}