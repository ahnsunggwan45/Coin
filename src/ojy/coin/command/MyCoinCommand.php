<?php

namespace ojy\coin\command;

use ojy\coin\Coin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class MyCoinCommand extends Command
{

    public function __construct()
    {
        parent::__construct("내코인", "나의 코인을 확인합니다.", "/내코인", ["mycoin", "내돈"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $sender->sendMessage(Coin::PREFIX . "보유중인 코인: " . Coin::koreanWonFormat(ceil(Coin::getCoin($sender))) . " 코인");
        }
    }
}