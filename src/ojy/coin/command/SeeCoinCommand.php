<?php

namespace ojy\coin\command;

use lang\Lang;
use ojy\coin\Coin;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;

class SeeCoinCommand extends CoinCommand
{

    public function __construct(Coin $plugin)
    {
        parent::__construct("코인보기", $plugin);
        $this->setAliases(["seecoin", "돈보기"]);
    }

    public function _execute(CommandSender $sender, array $args): bool
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("인게임에서만 실행해주세요.");
            return true;
        }
        if (!isset($args[0]))
            $name = $sender->getName();
        else {
            $name = array_shift($args);
            if (($player = Server::getInstance()->getPlayer($name)) !== null)
                $name = $player->getName();
        }
        $coin = Coin::getCoin($name);
        if ($coin === null) {
            $sender->sendMessage(Coin::PREFIX . Lang::translate(Coin::COIN_PREFIX . "타겟_미스", Lang::getLang($sender)));
            return true;
        }
        $sender->sendMessage(Coin::PREFIX . $name . Lang::translate(Coin::COIN_PREFIX . "see_코인", Lang::getLang($sender)) . $coin);
        return true;
    }
}