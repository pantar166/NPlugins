<?php

namespace NArmorColor;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\utils\Config;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\item\Item;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\event\server\DataPacketReceiveEvent;

class NArmorColor extends PluginBase implements Listener {

	public function onEnable () {
		@mkdir ( $this->getDataFolder () );
		$this->database = new Config ( $this->getDataFolder() . 'data.yml', Config::YAML, [
	 		'item' => [
				'id' => 399,
				'damage' => 0,
				'count' => 3
			]
		]);
		$this->db = $this->database->getAll();
		$this->addCommand (['갑옷']);
		$this->getServer ()->getPluginManager ()->registerEvents ( $this, $this );
	}

	public function addCommand ($array)
	{
		$count = 0;
		foreach ($array as $command) {
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
		$player->sendMessage ('§6§l 시스템 > §r§7'.$msg);
	}
	
	public function allmsg ($msg) {
		foreach ($this->getServer()->getOnlinePlayers() as $player) {
			$player->sendMessage ('§6§l 전체 알림 > §r§7'.$msg);
		}
	}
	
	public function getItem ()
	{
		return Item::jsonDeserialize ($this->db ['item']);
	}

	public function sendUI (Player $player, $code, $data) {
		$packet = new ModalFormRequestPacket();
		$packet->formId = $code;
		$packet->formData = $data;
		$player->dataPacket ($packet);		
	}

	public function msgUI ($player, $msg, $title = '< 네오스의 색 갑옷 상점 >')
	{
		$packet = new ModalFormRequestPacket();
		$packet->formId = 7452;
		$packet->formData = json_encode ([
			'type' => 'form',
			'title' => $title,
			'content' => "\n" . $msg . "\n\n\n",
			'buttons' => [
				[
					'text' => '[팝업 창 종료]'
				]
			]
		]);
		$player->dataPacket ($packet);
	}

	public function onRecievePacket (DataPacketReceiveEvent $event) {
		$packet = $event->getPacket ();
		$player = $event->getPlayer ();
        if (!$packet instanceof ModalFormResponsePacket) return true;
		$id = $packet->formId;
		$val = json_decode ($packet->formData, true);
		if ($id === 4545) {
			if ($val === true) return true;
			$ui = json_encode ([
				'type' => 'form',
				'title' => '< 네오스의 색 갑옷 상점 >',
				'content' => "\n" . '손에 들고 있는 갑옷의 색을 커스텀 합니다! 색을 골라주세요' . "\n\n\n",
				'buttons' => [
					[
						'text' => '- 하늘 색'
					],
					[
						'text' => '- 분홍 색'
					],
					[
						'text' => '- 노랑 색'
					],
					[
						'text' => '- 연두 색'
					],
					[
						'text' => '- 회 색'
					]
				]
			]);
			$this->sendUI ($player, 2323, $ui);
			return true;
		}
		if ($id === 2323) {
			if ($val === null) return true;
			$this->go ($player, str_replace ([0,1,2,3,4], ['하늘색','분홍색','노랑색','연두색','회색'],$val));
			return true;
		}
	}

	public function onCommand(CommandSender $player, Command $command, string $label, array $args): bool {
		$name = $player->getName();
		if ($command == '갑옷') {
			if (isset ($args[1]) && $args[0] === '비용' && $player->isOp()) {
				$item = $player->getInventory()->getItemInHand();
				if ($item->getId() === 0) {
					$this->msg ($player, '공기는 코인으로 설정할 수 없습니다!');
					return true;
				}
				$this->db ['item']['id'] = $item->getId();
				$this->db ['item']['damage'] = $item->getDamage();
				$this->db ['item']['count'] = $args[1];
				$this->msg ($player, '비용이 §b' . $item->getName() . ' ' . $args[1] . '개 §7로 변경되었습니다!');
				return true;
			}
			$ui = json_encode([
				'type' => 'modal',
				'title' => '< 네오스의 색 갑옷 상점 >',
				'content' => '구매하면 색이 변경된 가죽 갑옷 세트가 적용됩니다!' . "\n\n" . '변경할 때 ' . $this->getItem()->getName() . ' ' . $this->getItem()->getCount() . '개가 필요합니다!' . "\n\n" . 'Made by Ne0sW0rld (네오스)',
				'button1' => '[팝업 창 종료]',
				'button2' => '[메뉴로 이동]'
			]);
			$this->sendUI ($player, 4545, $ui);
		}
		return true;
	}

	public function go ($player, $color) {
		if (!$player->getInventory()->contains($this->getItem())) {
			$this->msgUI ($player, '코인이 부족하여 색을 바꿀 수 없습니다!' . "\n\n" . '§b> ' . $this->getItem()->getName() . ' ' . $this->getItem()->getCount() . ' 개 필요 <');
			return true;
		}
		$player->getInventory()->removeItem ($this->getItem());
		$this->setColor ($player, $color);
		$this->msgUI ($player, '성공적으로 색을 변경하였습니다');
	}

	public function setColor ($player, $color)
	{
		if ($color === '하늘색') {
			$array = [110, 227, 247];	
		}
		if ($color === '분홍색') {
			$array = [255, 178, 217];	
		}
		if ($color === '노랑색') {
			$array = [250, 237, 125];	
		}
		if ($color === '연두색') {
			$array = [134, 229, 127];	
		}
		if ($color === '회색') {
			$array = [76, 76, 76];	
		}
		$content = [Item::get(298,0,1), Item::get(299,0,1), Item::get(300,0,1), Item::get(301,0,1)];
		foreach ($content as $armor) {
			$armor->setCustomColor (new \pocketmine\utils\Color ($array[0], $array[1], $array[2]));
		}
		$player->getArmorInventory()->setContents ($content);
	}
	
}
