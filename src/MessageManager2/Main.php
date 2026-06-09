<?php

declare(strict_types=1);

namespace MessageManager2;

use pocketmine\entity\projectile\Arrow;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener{

public function onEnable() : void{
    $this->saveDefaultConfig();
    $this->getServer()->getPluginManager()->registerEvents($this, $this);

    $this->getLogger()->info("MessageManager2 enabled!");
}

public function onDeath(PlayerDeathEvent $event) : void{
    $this->getLogger()->info("Death event fired!");

    $player = $event->getPlayer();
    $cause = $player->getLastDamageCause();

        $message = (string) $this->getConfig()->getNested(
            "death-messages.default",
            "&c{player} died."
        );

        if($cause instanceof EntityDamageByEntityEvent){

            $damager = $cause->getDamager();

            if($damager instanceof Arrow){

                $owner = $damager->getOwningEntity();

                if($owner instanceof Player){
                    $message = (string) $this->getConfig()->getNested(
                        "death-messages.arrow"
                    );

                    $message = str_replace(
                        "{killer}",
                        $owner->getName(),
                        $message
                    );
                }

            }elseif($damager instanceof Player){

                $message = (string) $this->getConfig()->getNested(
                    "death-messages.player"
                );

                $message = str_replace(
                    "{killer}",
                    $damager->getName(),
                    $message
                );
            }

        }elseif($cause instanceof EntityDamageEvent){

            switch($cause->getCause()){

                case EntityDamageEvent::CAUSE_SUFFOCATION:
                    $message = (string) $this->getConfig()->getNested("death-messages.suffocation");
                    break;

                case EntityDamageEvent::CAUSE_FALL:
                    $message = (string) $this->getConfig()->getNested("death-messages.fall");
                    break;

                case EntityDamageEvent::CAUSE_FIRE:
                    $message = (string) $this->getConfig()->getNested("death-messages.fire");
                    break;

                case EntityDamageEvent::CAUSE_FIRE_TICK:
                    $message = (string) $this->getConfig()->getNested("death-messages.fire-tick");
                    break;

                case EntityDamageEvent::CAUSE_LAVA:
                    $message = (string) $this->getConfig()->getNested("death-messages.lava");
                    break;

                case EntityDamageEvent::CAUSE_DROWNING:
                    $message = (string) $this->getConfig()->getNested("death-messages.drowning");
                    break;

                case EntityDamageEvent::CAUSE_BLOCK_EXPLOSION:
                case EntityDamageEvent::CAUSE_ENTITY_EXPLOSION:
                    $message = (string) $this->getConfig()->getNested("death-messages.explosion");
                    break;

                case EntityDamageEvent::CAUSE_SUICIDE:
                    $message = (string) $this->getConfig()->getNested("death-messages.suicide");
                    break;

                case EntityDamageEvent::CAUSE_MAGIC:
                    $message = (string) $this->getConfig()->getNested("death-messages.magic");
                    break;

                case EntityDamageEvent::CAUSE_CONTACT:
                    $message = (string) $this->getConfig()->getNested("death-messages.contact");

                    $message = str_replace(
                        "{block}",
                        "a block",
                        $message
                    );
                    break;
            }
        }

$message = str_replace(
    "{player}",
    $player->getName(),
    $message
);

$this->getLogger()->info("Generated message: " . $message);

$event->setDeathMessage(
    TextFormat::colorize($message)
);
    }
}
