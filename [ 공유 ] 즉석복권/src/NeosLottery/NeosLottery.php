<?php 

namespace NeosLottery;

use pocketmine\plugin\PluginBase; 
use pocketmine\event\Listener; 
use pocketmine\block\Block;
use pocketmine\item\Item; 
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\event\player\PlayerInteractEvent;
use onebone\economyapi\EconomyAPI;

class NeosLottery extends PluginBase implements Listener {

public function onEnable() {
	
$this->getServer()->getPluginManager()->registerEvents ( $this, $this ); // 이벤트 사용

@mkdir ( $this->getDataFolder () );
$this->database = new Config ( $this->getDataFolder () . "setting.json", Config::JSON, [
"플러그인 칭호" => "§r§l§6[§fLottery§6] §r§f",
"복권 아이템" => "339:1"
]);
$this->db = $this->database->getAll ();
$this->db1 = new Config ( $this->getDataFolder () . "player.json", Config::JSON );
$this->db1 = $this->db1->getAll();
	
} // 퍼블릭 괄호	
		
public function TTTTOOOOOUUUUUCCCHHHHHH( PlayerInteractEvent $event) {
	
    $player = $event->getPlayer();
    $name = $player->getName();
	$tag = $this->db ["플러그인 칭호"];
	$i = explode ( ":", $this->db ["복권 아이템"] );

    if ( $event->getItem()->getId() == $i[0] and $event->getItem()->getDamage() == $i[1] ) {
	
        $player->getInventory()->removeItem(new Item($i[0], $i[1], 1));
	
    switch ( mt_rand ( 0, 25 ) ) {
		
    case 0:
        $this->getServer()->broadcastMessage("§b§l============================");
        $this->getServer()->broadcastMessage("§r §b[복권] §f{$name}님이 즉석복권에서 1등이 당첨되어, 100만원을 받습니다..!");
        $this->getServer()->broadcastMessage("§b§l============================");
        EconomyAPI::getInstance()->addMoney ( $player, 1000000 );
        break;
		
    case 1:
        $this->getServer()->broadcastMessage("§b§l============================");
        $this->getServer()->broadcastMessage("§r §b[복권] §f{$name}님이 즉석복권에서 2등이 당첨되어, 70만원을 받습니다..!");
        $this->getServer()->broadcastMessage("§b§l============================");
        EconomyAPI::getInstance()->addMoney ( $player, 700000 );
        break;
		
    case 2:
        $this->getServer()->broadcastMessage("§b§l============================");
        $this->getServer()->broadcastMessage("§r §b[복권] §f{$name}님이 즉석복권에서 3등이 당첨되어, 50만원을 받습니다..!");
        $this->getServer()->broadcastMessage("§b§l============================");
        EconomyAPI::getInstance()->addMoney ( $player, 500000 );
        break;
		
    case 3:
        $this->getServer()->broadcastMessage("§b§l============================");
        $this->getServer()->broadcastMessage("§r §b[복권] §f{$name}님이 즉석복권에서 4등이 당첨되어, 30만원을 받습니다..!");
        $this->getServer()->broadcastMessage("§b§l============================");
        EconomyAPI::getInstance()->addMoney ( $player, 300000 );
        break;
		
    case 4:
        $this->getServer()->broadcastMessage("§b§l============================");
        $this->getServer()->broadcastMessage("§r §b[복권] §f{$name}님이 즉석복권에서 1등이 당첨되어, 10만원을 받습니다..!");
        $this->getServer()->broadcastMessage("§b§l============================");
        EconomyAPI::getInstance()->addMoney ( $player, 100000 );
        break;
		
    case 5:
        $player->sendMessage ( $tag . "아쉽지만 복권에서 §c아무 것도 §f나오지 않으셨습니다..!" );
		break;
		
    case 6;
        $player->sendMessage ( $tag . "아쉽지만 복권에서 §c아무 것도 §f나오지 않으셨습니다..!" );
		break;
		
    case 7;
        $player->sendMessage ( $tag . "아쉽지만 복권에서 §c아무 것도 §f나오지 않으셨습니다..!" );
		break;
		
    case 8;
        $player->sendMessage ( $tag . "아쉽지만 복권에서 §c아무 것도 §f나오지 않으셨습니다..!" );
		break;
		
    case 9;
        $player->sendMessage ( $tag . "아쉽지만 복권에서 §c아무 것도 §f나오지 않으셨습니다..!" );
		break;
		
    case 10;
        $player->sendMessage ( $tag . "아쉽지만 복권에서 §c아무 것도 §f나오지 않으셨습니다..!" );
		break;
		
    case 11:
        $player->sendMessage ( $tag . "아쉽지만 복권에서 §c아무 것도 §f나오지 않으셨습니다..!" );
		break;
		
    case 12:
        $player->sendMessage ( $tag . "아쉽지만 복권에서 §c아무 것도 §f나오지 않으셨습니다..!" );
		break;
		
    case 13:
        $player->sendMessage ( $tag . "아쉽지만 복권에서 §c아무 것도 §f나오지 않으셨습니다..!" );
		break;
		
    case 14:
        $player->sendMessage ( $tag . "아쉽지만 복권에서 §c아무 것도 §f나오지 않으셨습니다..!" );
		break;
		
    case 15:
        $this->getServer()->broadcastMessage("§c§l============================");
        $this->getServer()->broadcastMessage("§r §c[복권] §f{$name}님이 즉석복권에서 만원을 날리셨습니다..!");
        $this->getServer()->broadcastMessage("§c§l============================");
        EconomyAPI::getInstance()->reduceMoney ( $player, 10000 );
		break;
		
    case 16:
        $this->getServer()->broadcastMessage("§c§l============================");
        $this->getServer()->broadcastMessage("§r §c[복권] §f{$name}님이 즉석복권에서 오천원을 날리셨습니다..!");
        $this->getServer()->broadcastMessage("§c§l============================");
        EconomyAPI::getInstance()->reduceMoney ( $player, 5000 );
		break;
		
    case 17:
        $this->getServer()->broadcastMessage("§c§l============================");
        $this->getServer()->broadcastMessage("§r §c[복권] §f{$name}님이 즉석복권에서 오만원을 날리셨습니다..!");
        $this->getServer()->broadcastMessage("§c§l============================");
        EconomyAPI::getInstance()->reduceMoney ( $player, 50000 );
		break;
		
    case 18:
        $this->getServer()->broadcastMessage("§c§l============================");
        $this->getServer()->broadcastMessage("§r §c[복권] §f{$name}님이 즉석복권에서 십만원을 날리셨습니다..!");
        $this->getServer()->broadcastMessage("§c§l============================");
        EconomyAPI::getInstance()->reduceMoney ( $player, 100000 );
		break;
		
    case 19:
        $this->getServer()->broadcastMessage("§c§l============================");
        $this->getServer()->broadcastMessage("§r §c[복권] §f{$name}님이 즉석복권에서 삼천원을 날리셨습니다..!");
        $this->getServer()->broadcastMessage("§c§l============================");
        EconomyAPI::getInstance()->reduceMoney ( $player, 3000 );
		break;
		
    }

  	return true;
	
	}
	
} // 퍼블릭 괄호



} // 클래스 괄호