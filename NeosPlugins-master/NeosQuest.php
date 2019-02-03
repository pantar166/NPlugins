<?php

/**
 * @name NeosQuest
 * @main NeosQuest\NeosQuest
 * @author ["#HashTag","NeosKR"]
 * @version 0.1
 * @api 4.0.0
 * @description This plugin is made by HashTag (NeosKR)
 */
namespace NeosQuest;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\scheduler\Task;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJumpEvent;
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
use pocketmine\event\player\PlayerToggleSneakEvent;
use NeosMoney\NeosMoney;

class NeosQuest extends PluginBase implements Listener
{

    private static $instance = null;

    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        $this->database = new Config($this->getDataFolder() . "data.yml", Config::YAML);
        $this->db = $this->database->getAll();
        $this->settings = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
            "인포 타이틀" => "§d§l콘피그에서 수정할 수 있습니다",
            "보상 아이템" => "341",
            "아이템 데미지" => 0
        ]);
        $this->setting = $this->settings->getAll();
        $this->getServer()
            ->getPluginManager()
            ->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new class($this) extends Task {

            private $owner;

            public function __construct(NeosQuest $owner)
            {
                $this->owner = $owner;
            }

            public function getOwner()
            {
                return $this->owner;
            }

            public function onRun($currentTick)
            {
                foreach (Server::getInstance()->getOnlinePlayers() as $player) {
					$tag = Server::getInstance()->getPluginManager()->getPlugin("NeosMoney");
					if (!isset($tag->db[strtolower($player->getName())])) continue;
                    $this->getOwner()->sendInfo($player);
                }
            }
        }, 20);
    }

    public function onLoad()
    {
        self::$instance = $this;
    }

    public function onDisable()
    {
        $this->database->setAll($this->db);
        $this->database->save();
    }

    public function a($player)
    {
        $name = strtolower($player->getName());
        return $this->db[$name]["퀘스트 번호"];
    }

    public function b($player)
    {
        $name = strtolower($player->getName());
        return $this->db[$name]["퀘스트 진행상황"];
    }

    public function c($num)
    {
        if ($num == 1 or $num == 2 or $num == 3 or $num == 4 or $num == 5 or $num == 6 or $num == 7 or $num == 8) {
            if ($num == 1) {
                return 5000;
            }
            if ($num == 2) {
                return 150;
            }
            if ($num == 3) {
                return 200;
            }
            if ($num == 4) {
                return 250;
            }
            if ($num == 5) {
                return 300;
            }
            if ($num == 6) {
                return 250;
            }
            if ($num == 7) {
                return 300;
            }
            if ($num == 8) {
                return 50;
            }
        } else {
            return "-";
        }
    }

    public function d($num)
    {
        if ($num == 1 or $num == 2 or $num == 3 or $num == 4 or $num == 5 or $num == 6 or $num == 7 or $num == 8) {
            if ($num == 1) {
                return "5000번 움직이기";
            }
            if ($num == 2) {
                return "블럭 150번 부수기";
            }
            if ($num == 3) {
                return "블럭 200번 터치하기";
            }
            if ($num == 4) {
                return "250번 웅크리기";
            }
            if ($num == 5) {
                return "300번 점프하기";
            }
            if ($num == 6) {
                return "나무 250개 캐기";
            }
            if ($num == 7) {
                return "석탄 300개 채취하기";
            }
            if ($num == 8) {
                return "다른 유저 50번 죽이기";
            }
        } else {
            return "모든 퀘스트 클리어";
        }
    }

    public function sendInfo(Player $player)
    {
        $name = $player->getName();
        $mplugin = Server::getInstance()->getPluginManager()->getPlugin("NeosMoney");
        $money = $mplugin->getMoney($name);
        if (isset($money)) {
            $a = $this->d($this->a($player));
            $b = $this->b($player);
            $c = $this->c($this->a($player));
            $mrank = $mplugin->getRank($name);
            $e = "§c퀘스트 - §f{$a}\n§c진행상황 - §f{$b}/{$c}\n";
            $f = "§c돈 - §f{$money}원 ({$mrank}위)\n";
            $g = "§c서버 동접 - §f" . count(Server::getInstance()->getOnlinePlayers()) . "명";
            $player->sendTip($e . $f . $g);
        }
    }

    public function msg($player, $msg)
    {
        $a = Server::getInstance()->getPluginManager()->getPlugin("PluginPrefix");
        $a->msg($player, "퀘스트", $msg);
    }

    public function onMove(PlayerMoveEvent $event)
    {
        $this->sendInfo($event->getPlayer());
    }

    public function up(Player $player)
    {
        $name = strtolower($player->getName());
        if ($this->b($player) == $this->c($this->a($player))) {
            $this->db[$name]["퀘스트 번호"] ++;
            $this->db[$name]["퀘스트 진행상황"] = 0;
            $player->addTitle("§c§lClear!", "퀘스트를 클리어했습니다!\n새로운 퀘스트가 당신을 기다립니다!");
            $player->getInventory()->addItem(Item::get(376, 0, 1));
        } else {
            $this->db[$name]["퀘스트 진행상황"] ++;
        }
    }

    public function up1(Player $player)
    {
        $name = strtolower($player->getName());
        if ($this->b($player) == $this->c($this->a($player))) {
            $this->db[$name]["퀘스트 번호"] ++;
            $this->db[$name]["퀘스트 진행상황"] = 0;
            $player->addTitle("§c§lClear!", "모든 퀘스트를 클리어했습니다!\n수고 하셨습니다!!!");
            $player->getInventory()->addItem(Item::get(376, 0, 2));
        } else {
            $this->db[$name]["퀘스트 진행상황"] ++;
        }
    }

    public function MoveQuest(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        if ($this->a($player) == 1) {
            $this->up($player);
        }
    }

    public function BreakQuest(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $code = $event->getBlock()->getId() . ":" . $event->getBlock()->getDamage();
        if ($this->a($player) == 2) {
            $this->up($player);
        }
        if ($this->a($player) == 6) {
            if ($code == "17:0" || $code == "17:1" || $code == "17:2" || $code == "17:3" || $code == "162:0" || $code == "162:1") {
                $this->up($player);
            }
        }
        if ($this->a($player) == 7) {
            if ($code == "16:0") {
                $this->up($player);
            }
        }
    }

    public function TouchQuest(PLayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        if ($this->a($player) == 3) {
            $this->up($player);
        }
    }

    public function SneakingQuest(PlayerToggleSneakEvent $event)
    {
        $player = $event->getPlayer();
        if ($this->a($player) == 4) {
            $this->up($player);
        }
    }

    public function JumpQuest(PlayerJumpEvent $event)
    {
        $player = $event->getPlayer();
        if ($this->a($player) == 5) {
            $this->up($player);
        }
    }

    public function KillQuest(PlayerDeathEvent $event)
    {
        $entity = $event->getPlayer();
        $cause = $entity->getLastDamageCause();
        if ($cause instanceof EntityDamageByEntityEvent || $cause instanceof EntityDamageByChildEntityEvent) {
            if ($this->a($cause->getDamager()) == 8) {
                $this->up1($cause->getDamager());
            }
        }
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $name = strtolower($player->getName());
        if (! isset($this->db[$name])) {
            $this->db[$name] = [];
            $this->db[$name]["퀘스트 번호"] = 1;
            $this->db[$name]["퀘스트 진행상황"] = 0;
        }
    }

    public static function getInstance()
    {
        return self::$instance;
    }
}
