<?php

// 안내 : 본 플러그인은 네오스가 제작하였으며 본 플러그인에는 라이선스가 존재하지 않습니다.
// 안내 : 플러그인 내의 소스가 더러울 수 있으니, 양해바랍니다.

namespace NeosItemPopup;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Utils;
use pocketmine\utils\config;
use pocketmine\event\player\PlayerItemHeldEvent;

class NeosItemPopup extends PluginBase implements Listener {
	
    public function onEnable(){
		
		@mkdir ( $this->getDataFolder () ); // 폴더 생성
		$this->database = new Config ( $this->getDataFolder () . "setting.yml", Config::YAML ); // setting.yml 생성
		$this->db = $this->database->getAll (); // 라이키 라이키 !!!
		
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
		// 이벤트 사용
		
    } // 이벤트 괄호
	
    public function onPlayerItemHeldEvent ( PlayerItemHeldEvent $event ) {
		
        $player = $event->getPlayer();
        $item = $event->getItem()->getId() . ":" . $event->getItem()->getDamage();
		// 아이템코드를 구하는 값
		
        if ( isset ( $this->db [$item] ) ) { // 만약 손에 들고 있는 아이템이 콘피그에 등록되어 있다면
		$player->sendPopup ( $this->db [$item] ); // 플레이어에게 콘피그에 등록되어있던 내용을 팝업으로 띄우기
		}
		
    }  // 이벤트 괄호
	
} // 클래스 괄호