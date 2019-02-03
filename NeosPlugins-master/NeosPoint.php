<?php

/**
 * @name NeosPoint
 * @main NeosPoint\NeosPoint
 * @author ["#HashTag","NeosKR"]
 * @version 0.1
 * @api 4.0.0  
 * @description This plugin is made by HashTag (NeosKR)
 */
 
namespace NeosPoint;

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
use pocketmine\entity\EffectInstance;

use NeosMoney\NeosMoney;

class NeosPoint extends PluginBase implements Listener {

	private static $instance = null;

	public function onEnable () {
		@mkdir ( $this->getDataFolder () );
		$this->database = new Config ( $this->getDataFolder () . "Point.yml", Config::YAML );
		$this->db = $this->database->getAll ();
		$this->settings = new Config ( $this->getDataFolder() . "config.yml", Config::YAML, [
			"기본 포인트" => 0
		]);
		$this->setting = $this->settings->getAll();
		$this->getServer ()->getPluginManager ()->registerEvents ( $this, $this );
        $this->getServer()
            ->getPluginManager()
            ->registerEvents($this, $this);
        $this->cmd = new \pocketmine\command\PluginCommand("포인트", $this);
        $this->cmd->setDescription("This plugin is made by #HashTag (NeosKR)");
        $this->getServer()
            ->getCommandMap()
            ->register("포인트", $this->cmd);
	}
	
	public function onLoad() {
		self::$instance = $this;
	}
	
	public function onDisable () {
		$this->database->setAll ($this->db);
		$this->database->save ();
	}
	
    public function msg($player, $msg)
    {
        $a = Server::getInstance()->getPluginManager()->getPlugin("PluginPrefix");
        $a->msg($player, "포인트", $msg);
    }
	
	public function onJoin (PlayerJoinEvent $event) {
		$player = $event->getPlayer ();
		$name = strtolower ($player->getName());
		if (! isset ($this->db [$name])) {
			$this->db [$name] = $this->setting ["기본 포인트"];
		}
	}
	
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		$name = $sender->getName();
		if ($command->getName() == "포인트") {
			if (! isset ($args[0])) {
				$this->msg ($sender, "포인트 내정보 | 나의 포인트 정보를 확인합니다");
				$this->msg ($sender, "포인트 보기 (플레이어) | 플레이어의 포인트 정보를 확인합니다");
				$this->msg ($sender, "포인트 주기 (플레이어) (양) | 플레이어에게 나의 포인트을 줍니다");
				$this->msg ($sender, "포인트 순위 (숫자) | 포인트을 많이 가지고 있는 유저를 확인합니다");
                $this->msg ($sender, "포인트 환전 (숫자) | 입력한 숫자/10원으로 환전합니다");
			} else if ($args[0] == "내정보") {
				$bbom = $this->getPoint ($name);
				$rank = $this->getRank ($name);
				$this->msg ($sender, "{$bbom}P (순위:{$rank}위) 를 소유하고 계십니다");
			} else if ($args[0] == "보기") {
				if (!isset ($args[1])) {
					$this->msg ($sender, "포인트 보기 (플레이어) | 플레이어가 소유한 포인트를 확인합니다");
				} else {
					$Point = $this->getPoint ($args[1]);
					$rank = $this->getRank ($args[1]);
					if ($bbom == "알 수 없음") {
						$this->msg ($sender, "{$args[1]}님은 서버에 접속하신 적이 없습니다");
					} else {
						$this->msg ($sender, "{$args[1]}님은 {$Point} 포인트 (순위:{$rank}위) 를 소유하고 계십니다");
					}
				}
			} else if ($args[0] == "주기") {
				if (!isset ($args[2])) {
					$this->msg ($sender, "포인트 주기 (플레이어) (양) | 플레이어에게 나의 포인트을 줍니다");
				} else {
					if ($args[2] > $this->getPoint ($name)) {
						$this->msg ($sender, "나의 포인트보다 주려는 포인트이 더 많습니다");
					} else {
						$this->setPoint ($name, $this->getPoint ($name) - $args[2]);
						$this->setPoint ($args[1], $this->getPoint ($args[1]) + $args[2]);
						$player2 = Server::getInstance()->getPlayer ($args[1]);
						$this->msg ($sender, "{$args[1]}님께 {$args[2]}포인트를 주었습니다");
						if (!$player2 == null) {
							$this->msg ($player2, "{$name}님께 {$args[2]}포인트를 받았습니다");
						}
					}
				}
			} else if ($args[0] == "환전") {
				if (!isset ($args[1])) {
					$this->msg ($sender, "포인트 환전 (숫자) | 입력한 숫자/10원으로 환전합니다");
				} else {
					if ($args[1] > $this->getPoint ($name)) {
						$this->msg ($sender, "나의 포인트보다 교환하려는 포인트이 더 많습니다");
					} else {
                        $zonzal = $args[1] / 10;
						$this->setPoint ($name, $this->getPoint ($name) - $args[1]);
						NeosMoney::getInstance()->setMoney ($sender->getName(), $zonzal+NeosMoney::getInstance()->getMoney ($sender->getName()));
                        $this->msg ($sender, "{$zonzal}원으로 환전했습니다");
					}
				}
			} else if ($args[0] == "순위") {
				$data = (array) $this->db;
				$maxpage = ceil(count($data)/5);
				if(!isset($args[1]) || !is_numeric($args[1]) || $args[1] < 1) 
					$page = 1;
				else if($maxpage < $args[1]) 
					$page = $maxpage;
				else
				$page = $args[1];
				$stIndex = ($page*5)-4;
				$edIndex = $page*5;
				
				arsort ($data);
				$string = "";
				$count = 0;
				foreach($data as $p => $k) {
					++$count;
					if($count < $stIndex) continue;
					if($count > $edIndex) break;
					$string .= "\n§r§c§l[{$count}위] §r§f{$p} §f> §f{$k} 포인트";
                }
                $sender->sendMessage("§c§l<===== §f포인트 순위 §c§l| §r§f{$page} §c§l/ §r§f{$maxpage} §c§l=====>§r{$string}");
			} else if ($args[0] == "설정") {
				if (!$sender->isOP()) {
					$this->msg ($sender, "이 명령어는 관리자 전용 명령어 입니다");
				} else {
					if (!isset ($args[2])) {
						$this->msg ($sender, "포인트 설정 (플레이어) (양) | 플레이어의 포인트을 설정합니다");
					} else {
						$this->setPoint ($args[1], $args[2]);
						$this->msg ($sender, "{$args[1]}님의 포인트를 {$args[2]}로 설정했습니다");
					}
				}
			} else if ($args[0] == "뺏기") {
				if (!$sender->isOP()) {
					$this->msg ($sender, "이 명령어는 관리자 전용 명령어 입니다");
				} else {
					if (!isset ($args[2])) {
						$this->msg ($sender, "포인트 뺏기 (플레이어) (양) | 플레이어의 포인트을 뺏어갑니다");
					} else {
						$this->setPoint ($args[1], $this->getPoint ($args[1]) - $args[2]);
						$this->msg ($sender, "{$args[1]}님의 포인트를 {$args[2]}만큼 뺏었습니다");
					}
				}
			}
		}
		return true;
	}
	
	public function getRank ($name) {
		$name = strtolower ($name);
        if (! isset ($this->db [$name])) {
			return "알 수 없음";
		}
		$data = (array) $this->db;
        arsort ($data);
        $count = 0;
        foreach ($data as $name1 => $total) {
			$count++;
			if ($name == $name1)
			return $count;
        }
	}
	
	public function getPoint ($name) {
		$name = strtolower ($name);
		return $this->db [$name];
	}
	
	public function setPoint ($name, $value) {
		$name = strtolower ($name);
		$this->db [$name] = $value;
	}
	
	public static function getInstance() {
		return self::$instance;
	}
	
}
