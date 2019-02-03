<?php

namespace UIName;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\level\Position;
use pocketmine\block\Block;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\Effect;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket; // 커스텀 UI 관련
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket; // 커스텀 UI 관련
use pocketmine\event\server\DataPacketReceiveEvent;

class UIName extends PluginBase implements Listener {
	
	public function onEnable() {
		$this->getServer ()->getPluginManager ()->registerEvents ( $this, $this );
	}

	public function dt0() {
        $text = [
            "type" => "custom_form",
            "title" => "§c§l[§f아이템§c] §f아이템 이름 변경 도구",
			"content" => [ 
				[ 
				    "type" => "input",
					"text" => "§c[§f아이템§c] §f원하는 아이템 이름 §c| §f멋지게 꾸며주세요!" 
				],
				[ 
				    "type" => "input",
					"text" => "§c[§f아이템§c] §f원하는 아이템 설명 §c| §f(줄바꿈) 으로 줄을 바꿀 수 있습니다!" 
				]				
				]			
            ];
		return json_encode ( $text );
	}
	
	public function ChangeItemName (DataPacketReceiveEvent $event) {
		$p = $event->getPacket ();
		$player = $event->getPlayer ();
		if ($p instanceof ModalFormResponsePacket and $p->formId == 3214 ) {
			$name = json_decode ( $p->formData, true );
			if ( $name[0] == null or $name[1] == null ) {
				$player->sendMessage ("§c[§f아이템§c] §f이름과 설명 모두 적어주세요!");
			} else {
				$item = $player->getInventory()->getItemInHand();
				$n = "§r".$name[0];
				$item->setCustomName($n);
				$item->setLore([str_replace ("(줄바꿈)", "\n", $name[1])]);
				$player->getInventory()->setItemInHand($item);
				$player->sendMessage ("§c[§f아이템§c] §f아이템의 속성을 변경하였습니다!");
				$player->sendMessage ("§c§lNAME >> §r§f".$name[0]);
				$player->sendMessage ("§c§lLORE >> §r§f".$name[1]);
			}
        }
	}
	
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		if ($command->getName () == "이름") {
			$p = new ModalFormRequestPacket ();
			$p->formId = 3214;
			$p->formData = $this->dt0();
			$sender->dataPacket ($p);
			return true;
		}
		return true;
	}
}
?>