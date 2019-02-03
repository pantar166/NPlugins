<?php

/**
 * @name SiwolPlunder
 * @main SiwolPlunder\SiwolPlunder
 * @author ["#HashTag","NeosKR"]
 * @version 0.1
 * @api 4.0.0
 * @description This plugin is made by HashTag (NeosKR)
 */
namespace SiwolPlunder;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\entity\Entity;

use pocketmine\utils\Config;

use pocketmine\item\Item;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;


use pocketmine\network\mcpe\protocol\AddEntityPacket;

use pocketmine\math\Vector3;
use pocketmine\level\Level;
use pocketmine\level\Position;

use pocketmine\level\particle\DustParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\LavaParticle;
use pocketmine\level\particle\RedstoneParticle;
use pocketmine\level\particle\BubbleParticle;

use pocketmine\level\Explosion;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class SiwolPlunder extends PluginBase implements Listener
{

	private $cool = [];
	
    public function onEnable()
    {
		@mkdir($this->getDataFolder());
        $this->database = new Config($this->getDataFolder() . "yaktal.yml", Config::YAML);
        $this->db = $this->database->getAll();
        $this->hpbase = new Config($this->getDataFolder() . "customHP.yml", Config::YAML);
        $this->hp = $this->hpbase->getAll();
        $this->database2 = new Config($this->getDataFolder() . "warps.yml", Config::YAML);
        $this->db2 = $this->database2->getAll();
		$this->command();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

	public function command ()
	{ 
		$this->cmd1 = new \pocketmine\command\PluginCommand("무기", $this);
        $this->cmd1->setDescription("This plugin is made by #HashTag (NeosKR)");
        $this->getServer()->getCommandMap()->register("무기", $this->cmd1);
		
		$this->cmd2 = new \pocketmine\command\PluginCommand("약탈", $this);
        $this->cmd2->setDescription("약탈 포인트 관리 플러그인 | Made by NeosKR");
        $this->getServer()->getCommandMap()->register("약탈", $this->cmd2);
		
		$this->cmd3 = new \pocketmine\command\PluginCommand("약탈존", $this);
        $this->cmd3->setDescription("약탈존으로 이동하는 명령어 입니다 | Made by NeosKR");
        $this->getServer()->getCommandMap()->register("약탈존", $this->cmd3);
		
		/*
		$a = new \pocketmine\command\PluginCommand(str_repeat ("/",2)."a", $this);
        $a->setDescription("§r§6§l§oSIWOL §fONLINE");
        $this->getServer()->getCommandMap()->register(str_repeat ("/",2)."a", $a);
		
		$b = new \pocketmine\command\PluginCommand(str_repeat ("/",2)."b", $this);
        $b->setDescription("§r§f< 시월 온라인 공식 밴드 >");
        $this->getServer()->getCommandMap()->register(str_repeat ("/",2)."b", $b);
	
		$c = new \pocketmine\command\PluginCommand(str_repeat ("/",2)."c", $this);
        $c->setDescription("§r§6§lURL - §fband.us/@mcage4");
        $this->getServer()->getCommandMap()->register(str_repeat ("/",2)."c", $c);
		
		$d = new \pocketmine\command\PluginCommand(str_repeat ("/",2)."d", $this);
        $d->setDescription("§r§f신입 유저들은 튜토리얼로 가주세요오~");
        $this->getServer()->getCommandMap()->register(str_repeat ("/",2)."d", $d);
		*/
	}
	
    public function onDisable()
    {
        $this->database->setAll($this->db);
        $this->database->save();
        $this->hpbase->setAll($this->hp);
        $this->hpbase->save();
        $this->database2->setAll($this->db2);
        $this->database2->save();
    }
	
	public function msg ($player, $a)
	{
		$player->sendMessage ("§6§l[Weapon] §r§7".$a);
	}
	
	public function help ($player)
	{
		$line = "§6무기 지급 <플레이어> <무기> §f| §6무기 종류에 '모두'를 입력하면 모든 무기를 줄 수 있습니다";
		$linee = "§6무기 목록 §f| §6무기는 계속 추가될 수도 있습니다";
		$this->msg ($player, $line);
		$this->msg ($player, $linee);
	}
	
	public function onDamage (EntityDamageEvent $event)
	{
		$this->d1 ($event);
		$this->d2 ($event);
		$this->d3 ($event);
	}
	  
	public function d1 ($event) {
		if ($event->getCause() == EntityDamageEvent::CAUSE_FALL) {
			$event->setCancelled();
			$event->getEntity()->setHealth ($event->getEntity()->getMaxHealth());
		}
	}

	public function d2 ($event) {
		if ($event instanceof EntityDamageByEntityEvent) {
			$player = $event->getEntity();
			$damager = $event->getDamager();
			if ($this->canYak ($player) == false && $this->canYak ($damager) == false) {
				$event->setBaseDamage(0);
				$event->setCancelled(true);
				$damager->sendMessage ("§6§l[Fight] §r§7스폰 월드에 있는 잔디 블럭이나 주황색 양털 위에서만 싸울 수 있습니다!");
			}
		}		
	}
	
	public function d3 ($event) {
		$player = $event->getEntity();
		if ($player instanceof Player) {
			if ($this->canYak ($player) == false) {
				if ($event instanceof EntityDamageByEntityEvent) {
					$event->setBaseDamage(0);
					$event->setCancelled(true);
				}
			}				
			if ($event->isCancelled () or $event->getBaseDamage() == 0 or $this->canYak ($player) == false) {
				$neos = "네오스";
			} else {
				if ($event instanceof EntityDamageByEntityEvent) {
					$damager = $event->getDamager();
					$dname = $damager->getName();
					$name = $player->getName();
					$damage = $event->getBaseDamage();
					$damage = $damage + $this->addDamage ($damager);
					
					$test = $damage * 5;
					$this->setPoint ($dname, $this->getPoint ($name) + $test);
					$damager->sendMessage ("§6§l[Fight] §r§6{$name} §7님에게 §6{$damage} §7만큼의 데미지를 주어 약탈 포인트가 §6{$test} §7만큼 추가됩니다");
					$this->getServer()->broadcastPopup ("§c§l[§fDamage§c] §r§c{$dname} §f님이 §c{$name} §f님에게 §c{$damage} §f만큼의 데미지를 주었습니다");

					$player->addTitle ("체력 감소", "- {$damage}");
					
					$event->setBaseDamage(0);
					$event->setCancelled(true);
					
					$this->dash ($player, 0.5, 0.6, 0.5);
					
					if (1 > $this->getHealth ($name) - $damage) {
						$player->kill();
						$this->setHealth ($name, $this->getMaxHealth ($name));
					} else {
						$this->deHealth ($name, $damage);
					}	
				} else {
					$name = $player->getName();
					$damage = $event->getBaseDamage();
					$player->addTitle ("체력 감소", "- {$damage}");
					$event->setBaseDamage(0);
					$event->setCancelled(true);
					$this->dash ($player, 0.5, 0.6, 0.5);
					if (1 > $this->getHealth ($name) - $damage) {
						$player->kill();
						$this->setHealth ($name, $this->getMaxHealth ($name));
					} else {
						$this->deHealth ($name, $damage);
					}					
				}
			}
		}
	}
	
	public function addDamage ($player) {
		
		$x = ( int ) round ( $player->x - 0.5 );
        $y = ( int ) round ( $player->y - 1 );
        $z = ( int ) round ( $player->z - 0.5 );
		
        $id = $player->getLevel()->getBlockIdAt($x, $y, $z);
		$damage = $player->getLevel()->getBlockDataAt($x, $y, $z);
		
		$name = $player->getName();
		$dmg = $this->getLevel ($name);
		
		if ($player->getLevel()->getFolderName() == "world") {
			if ($id == 35 && $damage == 1) {
				$dmg = $dmg + 1;
			}
		}
		
		return $dmg;
	}
	
	public function onMove (PlayerMoveEvent $event)
	{
        $player = $event->getPlayer ();
        $x = ( int ) round ( $player->x - 0.5 );
        $y = ( int ) round ( $player->y - 1 );
        $z = ( int ) round ( $player->z - 0.5 );
        $id = $player->getLevel()->getBlockIdAt($x, $y, $z);
		$this->gogo ($player);
        if ($id == 170) {
			if (mt_rand (0,4) == 2) {
				$player->getInventory()->addItem(Item::get(433,0,1));
				$player->addTitle ("§6§lCoin", "§f코인을 획득하였습니다");
			}
		}
	}

    public function getTicket (PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock()->getId();
		if ($block == 48) {
			if ($player->getInventory()->contains(Item::get(433, 0, 1600))){
				$player->getInventory()->removeItem (Item::get(433,0,1600));
				$player->getInventory()->addItem (Item::get(375,0,1));
				$player->sendMessage ("§6§l[Item] §r§7무기 뽑기권이 지급되었습니다");
			} else {
				$player->sendMessage ("§6§l[Item] §r§7무기 뽑기권으로 교환하기 위해서는 코인을 1600개 이상 소지하고 있어야 합니다");
			}
		}
	}
	
	public function move ($player) {
		$num = mt_rand (1,5);
		$pos = $this->db2 [$num];
		$pos = explode (":", $pos);
		$pos = new Position((float)$pos[0]+mt_rand(1,4),(float)$pos[1],(float)$pos[2]+mt_rand(1,4),$this->getServer()->getLevelByName($pos[3]));
		$player->teleport($pos,(float)$player->yaw,(float)$player->pitch);
		$player->sendMessage ("§6§l[Fight] §r§7약탈존으로 이동하였습니다! 아이템을 잃어버리지 않게 조심하세요");
	}
	
	public function getLevel($name)
    {
        $name = strtolower($name);
        if (! isset($this->db[$name])) {
            return "알 수 없음";
        }
		$point = $this->getPoint ($name);
        $level = (int) $point / 1500;
		return (int) $level;
    }
	
	public function getPoint ($name)
    {
        $name = strtolower($name);
        if (! isset($this->db[$name])) {
            return "알 수 없음";
        }
		$point = $this->db [$name];
		return $point;
    }
	
	public function getHealth ($name)
    {
        $name = strtolower($name);
        if (! isset($this->hp[$name])) {
            return "알 수 없음";
        }
		$point = $this->hp [$name] ["체력"];
		return $point;
    }
	
	public function getMaxHealth ($name)
    {
        $name = strtolower($name);
        if (! isset($this->hp[$name])) {
            return "알 수 없음";
        }
		$point = $this->hp [$name] ["최대"];
		return $point;
    }
	
	public function setHealth ($name, $point)
    {
        $name = strtolower($name);
		$this->hp [$name] ["체력"] = $point;
    }
	
	public function setMaxHealth ($name, $point)
    {
        $name = strtolower($name);
		$this->hp [$name] ["최대"] = $point;
    }
	
	public function deHealth ($name, $point)
    {
        $name = strtolower($name);
		$this->hp [$name] ["체력"] = $this->getHealth ($name) - $point;
		$player = $this->getServer()->getPlayer ($name);
		$player->setHealth ($player->getMaxHealth());
		if ($this->getHealth ($name) == 0) {
			$player->kill();
		}
    }	
	
	public function setPoint ($name, $point)
    {
        $name = strtolower($name);
		$this->db [$name] = $point;
    }
	
	public function canYak ($player)
    {
		
        $x = ( int ) round ( $player->x - 0.5 );
        $y = ( int ) round ( $player->y - 1 );
        $z = ( int ) round ( $player->z - 0.5 );
		
        $id = $player->getLevel()->getBlockIdAt($x, $y, $z);
		$damage = $player->getLevel()->getBlockDataAt($x, $y, $z);
		
		$bool = false;
		
		if ($player->getLevel()->getFolderName() == "world") {
			if ($id == 35 && $damage == 1) {
				$bool = true;
			}
			if ($id == 2 && $damage == 0) {
				$bool = true;
			}
		}
		
		return $bool;
		
    }
	
	public function onJoin (PlayerJoinEvent $event) {
		$name = strtolower($event->getPlayer()->getName());
		if (!isset($this->db[$name])) {
			$this->db [$name] = 0;
		}
		if (!isset($this->hp[$name])) {
			$this->hp [$name] ["최대"] = 50;
			$this->hp [$name] ["체력"] = 50;
		}
	}
	
	public function gogo ($player) {
		$name = strtolower ($player->getName());
		$s = str_repeat (" ", 50);
		$level = $this->getLevel ($name);
		$point = $this->getPoint ($name);
		if ($this->canYak ($player)) {
			$a = "§a§l온";
		} else {
			$a = "§c§l오프";
		}
		$player->sendPopup ("§6§l[§fHP§6] §r§f현재 체력 - ".$this->getHealth ($name)." / §7최대 체력 - ".$this->getMaxHealth ($name)."\n§6§l[§f약탈§6] §f모드 - {$a} §r§f/ 레벨 - §6{$level} §f/ §7포인트 - §6{$point}");
		//$player->sendTip ("§r{$s}§r§6§l[§f약탈 포인트§6]§r\n§r{$s}§r§6§l* §f레벨 - §6{$level}§r\n§r{$s}§r§6§l* §f포인트 - §6{$point}§r\n§r{$s}§r§6§l* §f약탈 모드 - §6{$a}§r\n§r{$s}§r§6§l* §f명령어 - /약탈§r\n");
	}
	
	public function onCommand(CommandSender $player, Command $command, string $label, array $args):bool {
		if ($command->getName() == "약탈") {
			if (isset ($args[0])) {
				if ($args[0] == "확인") {
					if (isset($args[1])) {
						$target = $args[1];
					} else {
						$target = $player->getName();
					}
					$level = $this->getLevel ($target);
					$point = $this->getPoint ($target);
					$player->sendMessage ("§6§l[Fight] §r§6{$target} §7님의 정보 §6| §7레벨 - §6Lv {$level}, §7누적 포인트 - §6Point {$point}");
				} else if ($args[0] == "순위") {
					$data = (array) $this->db;
					$maxpage = ceil(count($data) / 5);
					if (! isset($args[1]) || ! is_numeric($args[1]) || $args[1] < 1)
						$page = 1;
					else if ($maxpage < $args[1])
						$page = $maxpage;
					else
						$page = $args[1];
					$stIndex = ($page * 5) - 4;
					$edIndex = $page * 5;
					arsort($data);
					$string = "";
					$count = 0;
					foreach ($data as $p => $k) {
						++ $count;
						if ($count < $stIndex)
							continue;
						if ($count > $edIndex)
							break;
						$string .= "\n§6§l[{$count}위] §r§7{$p} §f- §6Lv {$this->getLevel($p)} / Point {$this->getPoint($p)}";
					}
					$player->sendMessage("§6§l<===== §f약탈 포인트 랭킹 §6§l| §r§f{$page} §6§l/ §r§f{$maxpage} §6§l=====>§r{$string}");
				} else if ($args[0] == "설정" && $player->isOp()) {
					if (isset($args[2])) {
						$this->setPoint ($args[1], $args[2]);
						$player->sendMessage ("§6§l[Fight] §r§7{$args[1]}님의 포인트를 {$args[2]}으로 설정했습니다");
					} else {
						$player->sendMessage ("§6§l[Fight] §r§7약탈 설정 <플레이어> <양> §6| §7플레이어의 약탈 포인트를 설정합니다");
					}
				} else {
					$player->sendMessage ("§6§l[Fight] §r§7약탈 확인 <플레이어> §6| §7플레이어의 약탈 정보를 확인합니다 (플레이어 입력란에 입력하지 않을 경우 자신의 정보를 확인합니다)");
					$player->sendMessage ("§6§l[Fight] §r§7약탈 순위 <페이지> §6| §7약탈 포인트 순위를 나열합니다 (페이지 입력란에 입력하지 않을 경우 1페이지를 출력합니다)");
					if ($player->isOp()) {
						$player->sendMessage ("§6§l[관리자 전용 명령어] §r§7약탈 설정 <플레이어> <양> §6| §7플레이어의 약탈 포인트를 설정합니다");
					}
				}
			} else {
				$player->sendMessage ("§6§l[Fight] §r§7약탈 확인 <플레이어> §6| §7플레이어의 약탈 정보를 확인합니다 (플레이어 입력란에 입력하지 않을 경우 자신의 정보를 확인합니다)");
				$player->sendMessage ("§6§l[Fight] §r§7약탈 순위 <페이지> §6| §7약탈 포인트 순위를 나열합니다 (페이지 입력란에 입력하지 않을 경우 1페이지를 출력합니다)");
				if ($player->isOp()) {
					$player->sendMessage ("§6§l[관리자 전용 명령어] §r§7약탈 설정 <플레이어> <양> §6| §7플레이어의 약탈 포인트를 설정합니다");
				}
			}
		}			
		if ($command->getName() == "약탈존") {
			if (isset($args[0]) && $player->isOp()) {
				$this->db2 [$args[0]] = $player->x.":".$player->y.":".$player->z.":".$player->level->getFolderName();
				$player->sendMessage ("§6§l[Fight] §r§7약탈존 ({$args[0]}번 지점) 이 설정되었습니다");
				$this->database2->setAll($this->db2);
				$this->database2->save();
			} else {
				$this->move ($player);
			}
		}
		if ($command->getName() == "무기") {
			if (isset ($args[0])) {
				if ($args[0] == "지급" && $player->isOp()) {
					if (isset ($args[2])) {
						if ($args[2] == "모두" && $player->isOp()) {
							$this->give ($player, 0);
							$this->give ($player, 1);
							$this->give ($player, 2);
							$this->give ($player, 3);
							$this->give ($player, 4);
							$this->give ($player, 5);
							$this->msg ($player, "모든 무기들이 인벤토리에 지급되었습니다");
						} else {
							$target = $this->getServer()->getPlayer ($args[1]);
							if ($target == null) {
								$this->msg ($player, "해당 플레이어는 현재 오프라인 상태입니다");
							} else {
								$this->give ($target, $args[2]);
								$this->msg ($player, "해당 플레이어에게 무기를 지급했습니다");
							}
						}
					} else {
						$this->help ($player);
					}
				} else if ($args[0] == "목록") {
					$this->msg ($player, "§6불의 저주 §f| §610칸 이내 유저들에게 불 소환");
					$this->msg ($player, "§6신의 번개 §f| §610칸 이내 유저들에게 번개 소환");
					$this->msg ($player, "§6마약의 대쉬 §f| §610칸 이내 유저들 대쉬");
					$this->msg ($player, "§6텔레포트 머신 §f| §610칸 이내 유저들을 자신에게 텔레포트");
					$this->msg ($player, "§6킬러의 플랜 §f| §610칸 이내 유저들의 체력을 반으로 만든다");
					$this->msg ($player, "§6켈리아 §f| §6신속을 5초간 지급하고 10칸 이내 유저들에게 3초간 구속을 준다");
				} else {
					$this->help ($player);
				}
			} else {
				$this->help ($player);
			}
		}
		return true;
	}
			
	public function makeTimestamp() {
		$date = date ( "Y-m-d H:i:s" );
		$yy = substr ( $date, 0, 4 );
		$mm = substr ( $date, 5, 2 );
		$dd = substr ( $date, 8, 2 );
		$hh = substr ( $date, 11, 2 );
		$ii = substr ( $date, 14, 2 );
		$ss = substr ( $date, 17, 2 );
		return mktime ( $hh, $ii, $ss, $mm, $dd, $yy );
	}
	
	public function addLightning ($player, $pos, $val) {
		$packet = new AddEntityPacket ();
		$packet->position = $pos;
		$packet->metadata = [ ];
		$packet->type = 93;
		$packet->entityRuntimeId = Entity::$entityCount ++;
		foreach ( $player->level->getPlayers () as $players ) {
			$players->dataPacket ($packet);
		}
		$this->addExplosion ($pos, $val);
	}
	
	public function addExplosion ($pos, $val) {
		$explosion = new Explosion($pos, $val);
		$explosion->explodeB();
	}
	
	public function dash ($player, $x, $y, $z) {
		$player->setMotion (new Vector3($x, $y, $z));
	}
	
	public function effect ($player, $code, $time, $a) {
		$time = 20 * $time;
		$player->addEffect(new EffectInstance(Effect::getEffect($code), $time, $a));
	}
	
    public function give(Player $player, $number)
    {
        $id = 0;
        $damage = 0;
        $count = 1;
        if ($number == 0) {
            $id = 377;
            $name = "§r§6[ §f약탈 §6] §f불의 저주";
            $lore = array(
                "§r§f이 무기를 사용하면 10칸 이내의\n유저들이 강도 3~5의\n불이 생긴다"
            );
        }
        if ($number == 1) {
            $id = 452;
            $name = "§r§6[ §f약탈 §6] §f신의 번개";
            $lore = array(
                "§r§f이 무기를 사용하면 10칸 이내의\n유저들에게 강도 3~5의\n번개가 소환된다"
            );
        }
        if ($number == 2) {
            $id = 371;
            $name = "§r§6[ §f약탈 §6] §f마약의 대쉬";
            $lore = array(
                "§r§f이 무기를 사용하면 10칸 이내의\n유저들을 대쉬시킨다"
            );
        }
        if ($number == 3) {
            $id = 337;
            $name = "§r§6[ §f약탈 §6] §f텔레포트 머신";
            $lore = array(
                "§r§f이 무기를 사용하면 10칸 이내의\n유저들을 자신에게 텔레포트 시킨다"
            );
        }
        if ($number == 4) {
            $id = 409;
            $name = "§r§6[ §f약탈 §6] §f킬러의 플랜";
            $lore = array(
                "§r§f이 무기를 사용하면 10칸 이내의\n유저들의 체력을 반으로 만든다"
            );
        }
        if ($number == 5) {
            $id = 336;
            $name = "§r§6[ §f약탈 §6] §f켈리아";
            $lore = array(
                "§r§f이 무기를 사용하면 신속을 주고\n10칸 이내의 유저들에게 구속을 준다"
            );
        }
        $item = Item::get($id, $damage, $count);
        $item->setCustomName($name);
        $item->setLore($lore);
        $player->getInventory()->addItem($item);
    }

    public function onHeld(PlayerItemHeldEvent $event)
    {
        $player = $event->getPlayer();
        $id = $event->getItem()->getId();
        $damage = $event->getItem()->getDamage();
        if ($player->getLevel()->getFolderName() == "yak") {
            if ($id == 377 && $damage == 0) { // 블레이즈 가루
                // null...
            }
        }
    }
    
    public function onTouch(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $id = $event->getItem()->getId();
        $damage = $event->getItem()->getDamage();
        if ($player->getLevel()->getFolderName() == "world") {
            if ($id == 377) { // 블레이즈 가루
                foreach ($player->getLevel()->getPlayers() as $target) {
                    if ($player->distance($target) < 10) {
						if ($this->canYak ($target) == true && $this->canYak ($player) == true) {
                        if ($target == $player) {
							$player->sendPopup ("");
						} else {
							$skill = "불의 저주";
							$this->msg ($player, "스킬 ({$skill}) 을 사용하였습니다");
							$this->msg ($target, $player->getName()."님께서 나에게 {$skill} 을(를) 사용하셨습니다");
							
							$player->getInventory()->removeItem (Item::get (377,0,1));
                            $target->setOnFire (mt_rand (3,5));
							$x = - \sin ( $player->yaw / 180 * M_PI );
							$z = \cos ( $player->yaw / 180 * M_PI );
							for ($i=1; $i<10; $i++){
								$v = new Vector3($player->x+$i*$x,$player->y+2,$player->z+$i*$z);
								$player->getLevel()->addParticle(new HeartParticle($v,0,0,0)); 
							}
                        }}
                    }
				}
            }
			if ($id == 452) { // 철덩이
				foreach ($player->getLevel()->getPlayers() as $target) {
                    if ($player->distance($target) < 10) {
						if ($this->canYak ($target) == true && $this->canYak ($player) == true) {
                        if ($target == $player) {
							$player->sendPopup ("");
						} else {
							
							$skill = "신의 번개";
							$this->msg ($player, "스킬 ({$skill}) 을 사용하였습니다");
							$this->msg ($target, $player->getName()."님께서 나에게 {$skill} 을(를) 사용하셨습니다");
							

							$player->getInventory()->removeItem (Item::get (452,0,1));
							$pos = new Position ($target->x,$target->y,$target->z,$target->level);
                            $this->addLightning ($target, $pos, mt_rand (1,3));
							$x = - \sin ( $player->yaw / 180 * M_PI );
							$z = \cos ( $player->yaw / 180 * M_PI );
							for ($i=1; $i<10; $i++){
								$v = new Vector3($player->x+$i*$x,$player->y+2,$player->z+$i*$z);
								$player->getLevel()->addParticle(new DustParticle($v,0,0,0)); 
							}
                        }
                    }}
				}
			}
			if ($id == 371) { // 금덩이
				foreach ($player->getLevel()->getPlayers() as $target) {
                    if ($player->distance($target) < 10) {
						if ($this->canYak ($target) == true && $this->canYak ($player) == true) {
                        if ($target == $player) {
							$player->sendPopup ("");
						} else {
							
							$skill = "마약의 대쉬";
							$this->msg ($player, "스킬 ({$skill}) 을 사용하였습니다");
							$this->msg ($target, $player->getName()."님께서 나에게 {$skill} 을(를) 사용하셨습니다");
							
							$player->getInventory()->removeItem (Item::get (371,0,1));
							$this->dash ($target, mt_rand(0,2), mt_rand (3,5), mt_rand (0,2));
							$x = - \sin ( $player->yaw / 180 * M_PI );
							$z = \cos ( $player->yaw / 180 * M_PI );
							for ($i=1; $i<10; $i++){
								$v = new Vector3($player->x+$i*$x,$player->y+2,$player->z+$i*$z);
								$player->getLevel()->addParticle(new FlameParticle($v,0,0,0)); 
							}
                        }
                    }}
				}
			}
			if ($id == 337) { // 점토
				foreach ($player->getLevel()->getPlayers() as $target) {
                    if ($player->distance($target) < 10) {
						if ($this->canYak ($target) == true && $this->canYak ($player) == true) {
                        if ($target == $player) {
							$player->sendPopup ("");
						} else {
							
							$skill = "텔레포트 머신";
							$this->msg ($player, "스킬 ({$skill}) 을 사용하였습니다");
							$this->msg ($target, $player->getName()."님께서 나에게 {$skill} 을(를) 사용하셨습니다");
							
							$player->getInventory()->removeItem (Item::get (337,0,1));
							$target->teleport ($player);
							$x = - \sin ( $player->yaw / 180 * M_PI );
							$z = \cos ( $player->yaw / 180 * M_PI );
							for ($i=1; $i<10; $i++){
								$v = new Vector3($player->x+$i*$x,$player->y+2,$player->z+$i*$z);
								$player->getLevel()->addParticle(new RedstoneParticle($v,0,0,0)); 
							}
                        }
                    }}
				}
			}
			if ($id == 409) { // 프린즈마린 조각
				foreach ($player->getLevel()->getPlayers() as $target) {
                    if ($player->distance($target) < 10) {
						if ($this->canYak ($target) == true && $this->canYak ($player) == true) {
                        if ($target == $player) {
							$player->sendPopup ("");
						} else {
							
							$skill = "킬러의 플랜";
							$this->msg ($player, "스킬 ({$skill}) 을 사용하였습니다");
							$this->msg ($target, $player->getName()."님께서 나에게 {$skill} 을(를) 사용하셨습니다");
							
							$player->getInventory()->removeItem (Item::get (409,0,1));
							$hp = (int) $target->getHealth()/2;
							$this->deHealth ($target, $hp);
							$x = - \sin ( $player->yaw / 180 * M_PI );
							$z = \cos ( $player->yaw / 180 * M_PI );
							for ($i=1;$i<180;$i++){
								$sin = sin($i/90*M_PI);
								$cos = cos($i/90*M_PI);
								$v = new Vector3($player->x+$sin*3,$player->y+1,$player->z+$cos*3);
								$player->getLevel()->addParticle(new LavaParticle($v,0,0,0)); 
							}
                        }
                    }}
				}
			}
			if ($id == 336) { // 벽돌
				foreach ($player->getLevel()->getPlayers() as $target) {
                    if ($player->distance($target) < 10) {
						if ($this->canYak ($target) == true && $this->canYak ($player) == true) {
                        if ($target == $player) {
							$player->sendPopup ("");
						} else {
							
							$skill = "켈리아";
							$this->msg ($player, "스킬 ({$skill}) 을 사용하였습니다");
							$this->msg ($target, $player->getName()."님께서 나에게 {$skill} 을(를) 사용하셨습니다");

							$player->getInventory()->removeItem (Item::get (336,0,1));
							$this->effect ($player, 1, 5, mt_rand(1,3));
							$this->effect ($target, 2, 3, mt_rand (2,4));
							for ($i=1;$i<180;$i++){
								$sin = sin($i/90*M_PI);
								$cos = cos($i/90*M_PI);
								$v = new Vector3($player->x+$sin*3,$player->y+1,$player->z+$cos*3);
								$player->getLevel()->addParticle(new BubbleParticle($v,0,0,0)); 
							}
                        }
                    }}
				}
			}
        }
    }
}
?>