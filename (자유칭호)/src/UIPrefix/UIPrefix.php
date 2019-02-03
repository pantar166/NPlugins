<?php

namespace UIPrefix;

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
use ifteam\RankManager\rank\RankProvider;

class UIPrefix extends PluginBase implements Listener {
	
	public function onEnable() {
		$this->getServer ()->getPluginManager ()->registerEvents ( $this, $this );
	    @mkdir ($this->getDataFolder());
		$this->database = new Config ( $this->getDataFolder() . "config.yml", Config::YAML, [
			"타이틀" => "§l§a[§8자유칭호§a] §8원하는 칭호를 입력해주세요!",
			"자유칭호권" => "1:0:1"
		]);
		$this->db = $this->database->getAll();
	}

	public function dt0() {
        $text = [
            "type" => "custom_form",
            "title" => $this->db ["타이틀"],
			"content" => [ 
				[ 
				    "type" => "input",
					"text" => "§a[§f자유칭호§a] §f원하는 칭호를 입력해주세요 §a| §f칭호는 §a환불할 수 없습니다" 
				]				
				]			
            ];
		return json_encode ( $text );
	}
	
	public function ChangeItemName (DataPacketReceiveEvent $event) {
		$p = $event->getPacket ();
		$player = $event->getPlayer ();
		if ($p instanceof ModalFormResponsePacket and $p->formId == 3216 ) {
			$name = json_decode ( $p->formData, true );
			if ( $name[0] == null ) {
				$player->sendMessage ("§a[§f자유칭호§a] §f원하는 칭호를 입력해주세요");
			} else {
				$rank = RankProvider::getInstance()->getRank($player);
				$rank->addPrefixs([$name[0]]);
				$rank->setPrefix($name[0]);
				$player->sendMessage ("§a[§f자유칭호§a] §f칭호가 설정되었습니다! 이제 채팅을 시작하세요!");
			}
        }
	}
	
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		if ($command->getName () == "자유칭호") {
			$item = explode(":",$this->db["자유칭호권"]);
			if ( $sender->getInventory()->contains(Item::get($item[0], $item[1], $item[2]))){
				$sender->getInventory()->removeItem(Item::get($item[0], $item[1], $item[2]));
				$p = new ModalFormRequestPacket ();
				$p->formId = 3216;
				$p->formData = $this->dt0();
				$sender->dataPacket ($p);
				return true;
			} else {
				$sender->sendMessage ("§a[§f자유칭호§a] §f자유칭호를 구매하기 위한 아이템이 부족합니다!");
			}
		}
		return true;
	}
}
?>