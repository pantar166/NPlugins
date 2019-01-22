namespace NEntityTag;
class NEntityTag extends \pocketmine\plugin\PluginBase implements \pocketmine\event\Listener {

    public function onEnable () {
        $this->getServer()->getPluginManager()->registerEvents ($this,$this);
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
        } else {
            $this->setNameTag($entity);
        }
    }

    public function onSpawn (\pocketmine\event\entity\EntityDeathEvent $event) {
        $entity = $event->getEntity();
        if ($entity instanceof \pocketmine\Player) {
        } else {
            $this->setNameTag($entity);
        }
    }
}
