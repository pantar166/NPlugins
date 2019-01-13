<?php

namespace NEntityTag;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;

class NEntityTag extends PluginBase implements Listener {

public function onEnable () {
    $this->getServer()->getPluginManager()->registerEvents ($this,$this);
}

public function setNameTag (EntityDamageEvent $event) {
    $entity = $event->getEntity();
    if ($entity instanceof Player) {
    } else {
      $tag = explode("\n", $entity->getNameTag()) [0];
      $text = '§c§lHP ∥ §r§f' . $entity->getHealth() . '§7 / ' . $entity->getMaxHealth();
      $entity->setNameTag($tag . "\n" . $text);
      $entity->setNameTagVisible(true);
      $entity->setNameTagAlwaysVisible(true);
    }
  }
}
?>
