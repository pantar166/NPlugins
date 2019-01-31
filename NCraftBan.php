<?php

namespace NeosCraftBan;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\utils\Config;

use pocketmine\item\Item;
use pocketmine\block\Block;

use pocketmine\event\inventory\CraftItemEvent;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class NeosCraftBan extends PluginBase implements Listener {

	public function onEnable () {
		@mkdir ( $this->getDataFolder () );
		$this->database = new Config ( $this->getDataFolder() . 'data.yml', Config::YAML, []);
		$this->db = $this->database->getAll();
		$this->commands = ['조합밴'];
		$this->addCommand();
		$this->getServer()->getPluginManager()->registerEvents ( $this, $this );
	}

	public function addCommand () {
		$count = 0;
		foreach ($this->commands as $command) {
			$count ++;
			$a = new \pocketmine\command\PluginCommand($command, $this);
			$a->setDescription('플러그인 제작자 ㅣ 네오스 (NeosKR)');
			$this->getServer()->getCommandMap()->register($command, $a);
		}
		$this->getLogger()->info ($count.'개의 명령어가 활성되었습니다');
		$this->getLogger()->info ('Plugin is made by NeosKR');
	}
	
	public function onDisable () {
		$this->save();
	}

	public function save() {
		$this->database->setAll ($this->db);
		$this->database->save ();
	}

	public function msg ($player, $msg) {
		$player->sendMessage ('§6§l 시스템 ∥ §r§7'.$msg);
	}
	
	public function allmsg ($msg) {
		foreach ($this->getServer()->getOnlinePlayers() as $player) {
			$player->sendMessage ('§6§l 전체 알림 ∥ §r§7'.$msg);
		}
	}

	public function help ($player) {
		$msg = [
			'조합밴 추가 <아이템코드:데미지> | 조합밴 목록에 입력한 아이템을 등록합니다',
			'조합밴 삭제 <아이템코드:데미지> | 조합밴 목록에 등록된 아이템을 삭제합니다',
			'조합밴 목록 | 조합밴 목록에 등록된 아이템들을 보여줍니다',
			'* 추가와 삭제에서 아이템코드를 비어두면 손에 있는 아이템으로 처리됩니다'
		];
		foreach ($msg as $a) {
			$this->msg ($player, $a);
		}
	}

	public function onCraft (CraftItemEvent $event) {
		$player = $event->getPlayer();
		if ($player->isOp()) return true;
        foreach ($event->getOutputs() as $items) {
			var_dump ($items);
			$code = $items->getId() . ':' . $items->getDamage();
			if (isset($this->db [$code])) {
				$event->setCancelled(true);
				$this->msg ($player, '해당 아이템 (' . $items->getName() . ') 은 관리자에 의해 조합이 금지되었습니다');
			}
		}
	}

    public function onCommand(CommandSender $player, Command $command, string $label, array $args): bool {
		$name = $player->getName();
		if ($command == '조합밴' && $player->isOp()) {
            if (isset($args[0])) {
				if ($args[0] === '추가') {
					if (isset($args[1])) {
						$item = $args[1];
					} else {
						$item = $player->getInventory()->getItemInHand()->getId() . ':' . $player->getInventory()->getItemInHand()->getDamage();
					}
					if (isset($this->db[$item])) {
						$this->msg ($player, '이미 조합밴 목록에 등록되어 있습니다');
					} else {
						$this->db[$item] = $player->getName();
						$this->msg ($player, '아이템을 조합밴 목록에 추가했습니다');
					}
				} else if ($args[0] === '삭제') {
					if (isset($args[1])) {
						$item = $args[1];
					} else {
						$item = $player->getInventory()->getItemInHand()->getId() . ':' . $player->getInventory()->getItemInHand()->getDamage();
					}
					if (isset($this->db[$item])) {
						unset ($this->db[$item]);
						$this->msg ($player, '해당 아이템을 조합밴 목록에서 제거하였습니다');
					} else {
						$this->msg ($player, '해당 아이템은 조합밴 목록에 존재하지 않습니다');
					}					
				} else if ($args[0] === '목록') {
					$count = 0;
					foreach ($this->db as $item => $player2) {
						$count ++;
						$player->sendMessage ('§6[' . $count . '] §7' . $item . ' (' . $player2 . '님에 의해 등록됨)');
					}			
				} else {
					$this->help ($player);
				}
			} else {
				$this->help ($player);
			}
        }
        return true;
    }
}
