<?php 

namespace NeosDash;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\level\particle\HeartParticle;
use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class NeosDash extends PluginBase implements Listener{

    public function onEnable () {
        @mkdir ($this->getDataFolder());
        $this->database = new Config ($this->getDataFolder()."data.yml",Config::YAML,[
            "57:0" => "7"
        ]);
        $this->db = $this->database->getAll();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    public function save () {
        $this->database->setAll ($this->db);
        $this->database->save();
    }
    
    public function onDisable () {
		$this->save ();
	}
    
    public function onCommand (CommandSender $sender, Command $command, string $label, array $args):bool {
        if ($command->getName() == "대쉬" and $sender->isOp()) {
            if (isset ($args[1])) {
                if ($args[0] == "추가") {
                    $this->db [$args[1]] = $args[2];
                    $sender->sendMessage ("§b§lDash> §r§b{$args[1]} §7블럭를 밟으면 §b{$args[2]} §7칸을 대쉬하도록 설정하였습니다");
                    $this->save ();
                }
                if ($args[0] == "삭제") {
                    unset ($this->db [$args[1]]);
                    $sender->sendMessage ("§b§lDash> §r§b{$args[1]} §7블럭에 입력된 대쉬를 삭제하였습니다");
                    $this->save ();
                }
            } else {
                $sender->sendMessage ("§b§lDash> §r§7/대쉬 추가 (아이템코드:데미지) (대쉬할 양)");
                $sender->sendMessage ("§b§lDash> §r§7/대쉬 삭제 (아이템코드:데미지)");
            }
        }
        return true;
    }
    
    public function onMove (PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        $block = $player->getLevel()->getBlock(new Vector3($player->getX(), $player->getY() - 1, $player->getZ()));
        $code = $block->getId().":".$block->getDamage();
        if (isset ($this->db [$code])) {
            $player->setMotion ($player->getDirectionVector()->multiply($this->db [$code]));
        }
    }
}