<?php

/**
 * @name SaveNBT
 * @main SaveNBT\SaveNBT
 * @author ['#HashTag','NeosKR']
 * @version 0.1
 * @api 4.0.0
 * @description This plugin is made by HashTag (NeosKR)
 */
 
namespace SaveNBT;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\utils\Config;

use pocketmine\item\Item;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class SaveNBT extends PluginBase implements Listener
{
	
    public function onEnable()
    {
		@mkdir($this->getDataFolder());
        $this->database = new Config($this->getDataFolder() . 'data.yml', Config::YAML);
        $this->db = $this->database->getAll();
		$this->command ('아이템');
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

	public function command ($name)
	{ 
		$command = new \pocketmine\command\PluginCommand($name, $this);
        $command->setDescription('아이템 명령어 | 플러그인 구매 문의 카카오톡 n4kr');
        $this->getServer()->getCommandMap()->register($name, $command);
	}
	
    public function onDisable()
    {
        $this->database->setAll($this->db);
        $this->database->save();
    }
	
	public function msg ($player, $a)
	{
		$player->sendMessage ('§6§l[Item] §r§7'.$a);
	}
	
	public function onCommand(CommandSender $player, Command $command, string $label, array $args):bool
	{		
		if ($command->getName() == '아이템') {
			if (isset($args[2])) {
				if ($args[0] == '등록') {
					$item = $player->getInventory()->getItemInHand();
					$this->db [$args[1]] = [];
					$this->db [$args[1]]['id'] = $item->getId();
					$this->db [$args[1]]['damage'] = $item->getDamage();
					$this->db [$args[1]]['count'] = $item->getCount();
					$this->db [$args[1]]['nbt'] = $this->getNBT ($item);
					$this->db [$args[1]]['password'] = $args[2];
					$this->msg ($player, '아이템이 저장되었습니다 /아이템 받기 '.$args[1].' '.$args[2].'를 입력하여 아이템을 받으실 수 있습니다');
					$player->getInventory()->removeItem ($item);
				} else if ($args[0] == '받기') {
					if (isset($this->db[$args[1]])) {
						if ($this->db[$args[1]]['password'] == $args[2]) {
							$item = $this->getItem ($this->db [$args[1]]);
							$player->getInventory()->addItem ($item);
							unset ($this->db[$args[1]]);
							$this->msg ($player, '아이템을 받아, 리스트에서 삭제되었습니다!');
						} else {
							$this->msg ($player, '패스워드가 잘못되었습니다! 확인 바랍니다');
						}
					} else {
						$this->msg ($player, '해당 이름으로 등록된 아이템이 존재하지 않습니다! 확인 바랍니다');
					}
				} else {
					$this->msg ($player, '아이템 등록 <원하는 이름> <비밀번호> | 아이템을 저장소에 등록합니다');
					$this->msg ($player, '아이템 받기 <저장된 이름> <비밀번호> | 저장소에 등록된 아이템을 받습니다');
				}
			} else {
				$this->msg ($player, '아이템 등록 <원하는 이름> <비밀번호> | 아이템을 저장소에 등록합니다');
				$this->msg ($player, '아이템 받기 <저장된 이름> <비밀번호> | 저장소에 등록된 아이템을 받습니다');
			}
		}
		return true;
	}
			
	public function getNBT ($item)
	{
		return $item->getCompoundTag();
	}
	
	public function getItem ($item)
	{
		return Item::jsonDeserialize($item);
	}
	
}
?>