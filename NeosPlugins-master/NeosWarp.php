<?php

/**
 * @name NeosWarp
 * @main NeosWarp\NeosWarp
 * @author ["#HashTag","NeosKR"]
 * @version 0.1
 * @api 4.0.0  
 * @description This plugin is made by HashTag (NeosKR)
 */
namespace NeosWarp;

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

class NeosWarp extends PluginBase implements Listener
{

    private static $instance = null;

    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        $this->database = new Config($this->getDataFolder() . "warps.yml", Config::YAML);
        $this->db = $this->database->getAll();
        $this->lockbase = new Config($this->getDataFolder() . "locks.yml", Config::YAML);
        $this->lock = $this->lockbase->getAll();
        $this->settings = new Config($this->getDataFolder() . "settings.yml", Config::YAML, [
            "타이틀" => "§c§l《 §f워프 §c》§r",
            "서브 타이틀" => "§c(워프) §f(으)로 이동하였습니다"
        ]);
        $this->setting = $this->settings->getAll();
        $this->getServer()
            ->getPluginManager()
            ->registerEvents($this, $this);
        $this->cmd = new \pocketmine\command\PluginCommand("워프", $this);
        $this->cmd->setDescription("This plugin is made by #HashTag (NeosKR)");
        $this->getServer()
            ->getCommandMap()
            ->register("워프", $this->cmd);
    }

    public function onLoad()
    {
        self::$instance = $this;
    }

    public function onDisable()
    {
        $this->database->setAll($this->db);
        $this->database->save();
        $this->lockbase->setAll($this->lock);
        $this->lockbase->save();
    }

    public function msg($player, $msg)
    {
        $a = Server::getInstance()->getPluginManager()->getPlugin("PluginPrefix");
        $a->msg($player, "워프", $msg);
    }

    public function getWarps()
    {
        return $this->db;
    }

    public function warp($name, $player)
    {
        if (!empty ($name) && isset($this->db[$name])) {
            if (! $player->isOp() && isset($this->lock[$name])) {
                $this->msg($player, "{$name}라는 워프는 {$this->lock [$name]}님에 의해 잠겨있습니다");
            } else {
                $a = explode(":", $this->db[$name]);
                $pos = new Position((float) $a[0], (float) $a[1], (float) $a[2], $this->getServer()->getLevelByName($a[3]));
                $player->teleport($pos, (float) $player->yaw, (float) $player->pitch);
                $player->addTitle($this->setting["타이틀"], str_replace ("(워프)", $name, $this->setting["서브 타이틀"]));
                $this->msg($player, "{$name} (으)로 워프했습니다");
            }
        }
    }
	
	public function onWarp (PlayerCommandPreprocessEvent $event) {
		$player = $event->getPlayer();
		$command = explode ("/",$event->getMessage());
		if (isset ($command [1])) {
			if (isset ($this->db [$command[1]])) {
				$this->warp($command[1], $player);
				$event->setCancelled();
			}
		}
	}
	
    public function onCommand(CommandSender $player, Command $command, string $label, array $args): bool
    {
        $name = $player->getName();
        if ($command->getName() == "워프") {
            if (! isset($args[0])) {
                if ($player->isOp()) {
                    $this->msg($player, "워프 (이름) | 해당 워프로 이동합니다");
                    $this->msg($player, "워프 생성 | 워프를 생성합니다");
                    $this->msg($player, "워프 삭제 | 워프를 삭제합니다");
                    $this->msg($player, "워프 잠금 | 워프를 잠금 설정 합니다");
                    $this->msg($player, "워프 목록 | 워프 목록을 확인합니다");
                } else {
                    $this->msg($player, "워프 (이름) | 해당 워프로 이동합니다");
                    $this->msg($player, "워프 목록 | 워프 목록을 확인합니다");
                }
            } else {
				if (isset ($this->db [$args[0]])) {
                    $this->warp($args[0], $player);
                } else if ($args[0] == "생성") {
                    if ($player->isOp()) {
                        if (isset($args[1])) {
                            if (isset($args[2])) {
                                $this->msg($player, "워프 이름에 띄어쓰기를 사용하실 수 없습니다");
                            } else {
                                if (isset($this->db[$args[1]])) {
                                    $this->msg($player, "이미 해당 워프는 존재합니다");
                                } else {
                                    $x = $player->x;
                                    $y = $player->y;
                                    $z = $player->z;
                                    $l = $player->level->getFolderName();
                                    $pos = $x . ":" . $y . ":" . $z . ":" . $l;
                                    $this->db[$args[1]] = $pos;
                                    $this->msg($player, "워프 ({$args[1]})를 생성하였습니다");
                                }
                            }
                        } else {
                            $this->msg($player, "워프 생성 | 워프를 생성합니다");
                        }
                    } else {
                        $this->msg($player, "관리자만 사용 가능한 명령어 입니다");
                    }
                } else if ($args[0] == "삭제") {
                    if ($player->isOp()) {
                        if (isset($args[1])) {
                            if (isset($args[2])) {
                                $this->msg($player, "워프 이름에 띄어쓰기가 있는 워프는 없습니다");
                            } else {
                                if (isset($this->db[$args[1]])) {
                                    unset($this->db[$args[1]]);
                                    $this->msg($player, "워프 ({$args[1]})를 삭제하였습니다");
                                } else {
                                    $this->msg($player, "{$args[1]}라는 워프는 없습니다");
                                }
                            }
                        } else {
                            $this->msg($player, "워프 삭제 | 워프를 삭제합니다");
                        }
                    } else {
                        $this->msg($player, "관리자만 사용 가능한 명령어 입니다");
                    }
                } else if ($args[0] == "잠금") {
                    if ($player->isOp()) {
                        if (isset($args[1])) {
                            if (isset($args[2])) {
                                $this->msg($player, "워프 이름에 띄어쓰기가 있는 워프는 없습니다");
                            } else {
                                if (isset($this->db[$args[1]])) {
                                    if (isset($this->lock[$args[1]])) {
									unset ($this->lock [$args[1]]);
                                        $this->msg($player, "워프 ({$args[1]})를 잠금 해제하였습니다");
                                    } else {
                                        $this->lock[$args[1]] = $player->getName();
                                        $this->msg($player, "워프 ({$args[1]})를 잠금하였습니다");
                                    }
                                } else {
                                    $this->msg($player, "{$args[1]}라는 워프는 없습니다");
                                }
                            }
                        } else {
                            $this->msg($player, "워프 잠금 | 워프를 잠금 설정 합니다");
                        }
                    } else {
                        $this->msg($player, "관리자만 사용 가능한 명령어 입니다");
                    }
                } else if ($args[0] == "목록") {
                    $a = "";
                    $b = $this->getWarps();
                    foreach ($b as $name => $key) {
                        $a .= $name . ", ";
                    }
                    $c = Server::getInstance()->getPluginManager()->getPlugin("PluginPrefix");
                    $c->msg($player, "목록", $a);
                }
            }
        }
        return true;
    }

    public static function getInstance()
    {
        return self::$instance;
    }
}