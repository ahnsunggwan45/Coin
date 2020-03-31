<?php

namespace ojy\coin;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Server;

class EventListener implements Listener
{

    public function __construct()
    {
        Server::getInstance()->getPluginManager()->registerEvents($this, Coin::getInstance());
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        if (!isset(Coin::$coin[strtolower($player->getName())])) {
            Server::getInstance()->getLogger()->info("코인 데이터를 작성합니다: §b{$player->getName()}");
            Coin::$coin[strtolower($player->getName())] = Coin::$setting->get("default-coin");
        }
    }
}