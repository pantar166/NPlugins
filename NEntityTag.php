namespace NEntityTag;
class NEntityTag extends \pocketmine\plugin\PluginBase implements \pocketmine\event\Listener {

    public function onEnable () {
        $this->getServer()->getPluginManager()->registerEvents ($this,$this);
    }

    public function onJoin (\pocketmine\event\player\PlayerJoinEvent $event) {
        $event->getPlayer()->sendMessage ('§b▶ §f이 서버는 §bNeosPlugins §f를 사용 중 입니다.');
    }

    public function setNameTag ($entity) {
        $tag = explode("\n", $entity->getNameTag()) [0];
        $text = '§c§lHP ∥ §r§f' . $entity->getHealth() . '§7 / ' . $entity->getMaxHealth();
        $entity->setNameTag($tag . "\n" . $text);
        $entity->setNameTagVisible(true);
        $entity->setNameTagAlwaysVisible(true);
    }

    public function onDamage (\pocketmine\event\entity\EntityDamageEvent $event) {
        $entity = $event->getEntity();
        if ($entity instanceof \pocketmine\Player) {
            return true;
        }
        if ($this->getServer()->getPluginManager()->getPlugin('NDungun') === null) {
            return true;
        }
        if ($this->getServer()->getPluginManager()->getPlugin('NDungun')->CheckNPC($entity) === true) {
            return true;
        }
        $this->setNameTag($entity);
    }

    public function onSpawn (\pocketmine\event\entity\EntityDeathEvent $event) {
        $entity = $event->getEntity();
        if ($entity instanceof \pocketmine\Player) {
        } else {
            $this->setNameTag($entity);
        }
    }
}
