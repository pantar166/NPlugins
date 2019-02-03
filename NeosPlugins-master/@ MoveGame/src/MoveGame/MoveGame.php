<?php

namespace MoveGame;

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


class MoveGame extends PluginBase implements Listener{
     
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir ($this->getDataFolder());
        $this->database= (new Config ( $this->getDataFolder () . "config.yml", Config::YAML, [ 
            "1번 스폰" => [ "x" => 1, "y" => 1, "z" => 1 ],
            "2번 스폰" => [ "x" => 1, "y" => 1, "z" => 1 ],
            "3번 스폰" => [ "x" => 1, "y" => 1, "z" => 1 ],
            "4번 스폰" => [ "x" => 1, "y" => 1, "z" => 1 ],
            "5번 스폰" => [ "x" => 1, "y" => 1, "z" => 1 ]
        ] ));
        $this->db = $this->database->getAll ();
    }

    public function onDisable() {
        $this->database->setAll($this->db);
        $this->database->save();
    }
    
    public function t (Player $player) {
        $r = mt_rand (1,5);
        $level = $this->getServer()->getLevelByName("pvp");
        $a = $this->db [$r."번 스폰"];
        $player->teleport (new Position ($a["x"], $a["y"], $a["z"], $level));
        $player->sendMessage ("§b§l[§f전투§b] §r§f{$r}번 스폰으로 이동했습니다!");
    }
              
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args):bool{
        if ($command->getName() == "p") {
            if (isset ($args[1])) {
                if ($args[0] == "스폰설정") {
                    $this->db [$args[1]."번 스폰"] ["x"] = $sender->getX();
                    $this->db [$args[1]."번 스폰"] ["y"] = $sender->getY();
                    $this->db [$args[1]."번 스폰"] ["z"] = $sender->getZ();
                    $sender->sendMessage ("스폰설정 완료");
                }
                if ($args[0] == "미로") {
                    $this->db ["미".$args[1]."번 스폰"] ["x"] = $sender->getX();
                    $this->db ["미".$args[1]."번 스폰"] ["y"] = $sender->getY();
                    $this->db ["미".$args[1]."번 스폰"] ["z"] = $sender->getZ();
                    $sender->sendMessage ("스폰설정 완료");
                }
                if ($args[0] == "이동") {
                    $r = $args[1];
                    $level = $this->getServer()->getLevelByName("pvp");
                    $a = $this->db [$r."번 스폰"];
                    $player->teleport (new Position ($a["x"], $a["y"], $a["z"], $level));
                    $sender->sendMessage ("이동 완료");
                }
            } else {
                $sender->sendMessage ("p 스폰설정 1~5\np 이동 1~5");
            }
        }
        if ($command->getName() == "pv") {
            if (!isset ($args[0])) {
                $this->t ($sender);
            } else {
                $r = mt_rand (1,6);
                $level = $this->getServer()->getLevelByName("컨텐츠");
                $a = $this->db ["미".$r."번 스폰"];
                $sender->teleport (new Position ($a["x"], $a["y"], $a["z"], $level));
            }
            
        }
        return true;
    }
            
	public function onMove (PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        if ($player->getLevel()->getFolderName() == "pvp"){
            if ($player->getY() <= 5) {
                $player->sendMessage ("§b§l[§f전투§b] §r§fY좌표가 5 이하로 떨어져서 다시 시작합니다");
                $player->setHealth ($player->getMaxHealth());
                $this->t ($player);
            }
        }
    }

}