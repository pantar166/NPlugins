<?php

/**
 * @name NeosYakantoosi
 * @main NeosYakantoosi\NeosYakantoosi
 * @author ["#HashTag","NeosKR"]
 * @version 0.1
 * @api 4.0.0  
 * @description This plugin is made by HashTag (NeosKR)
 */
 
namespace NeosYakantoosi;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class NeosYakantoosi extends PluginBase implements Listener {
	
	public function onEnable() {
		$this->getServer()->getPluginManager ()->registerEvents ($this, $this);
        $this->cmd = new \pocketmine\command\PluginCommand("야간투시", $this);
        $this->cmd->setDescription("This plugin is made by #HashTag (NeosKR)");
        $this->getServer()->getCommandMap()->register("야간투시", $this->cmd);
	}
	
	public function Effect (Player $player) {
		$player->addEffect(new EffectInstance(Effect::getEffect(16), 10000, 5));
        $a = Server::getInstance()->getPluginManager()->getPlugin("PluginPrefix");
        $a->msg ($player, "야간투시", "야간투시 지급이 완료 되었습니다 !");
	}

	public function onHeld (PlayerItemHeldEvent $event) {
		$player = $event->getPlayer();
		if ($event->getItem()->getId() == 50){
			$this->Effect ($player);
		}
	}
		
	public function onCommand (CommandSender $player, Command $command, string $label, array $args):bool{
		if ($command->getName() == "야간투시"){
			$this->Effect ($player);
		}
		return true;
	}
	
}

?>