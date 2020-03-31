<?php

namespace ojy\coin\command;

use ojy\coin\Coin;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\Server;

class SetCoinCommand extends CoinCommand
{

    public function __construct(Coin $plugin)
    {
        parent::__construct("코인설정", $plugin);
        $this->setAliases(["setcoin", "돈설정"]);
        $this->setUsage("/코인설정 [닉네임] [코인]");
        $this->setDescription("플레이어의 코인을 설정합니다.");
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function _execute(CommandSender $sender, array $args): bool
    {
        if (!$sender instanceof Player) return true;
        if (!$sender->hasPermission($this->getPermission())) return true;

        if (!isset($args[0])) {
            $sender->sendMessage(Coin::PREFIX . $this->getUsage());
            return true;
        }
        $name = array_shift($args);
        if (($player = Server::getInstance()->getPlayer($name)) !== null)
            $name = $player->getName();
        $coin = Coin::getCoin($name);
        if ($coin === null) {
            $sender->sendMessage(Coin::PREFIX . $name . " 님은 서버에 접속한적이 없습니다.");
            return true;
        }
        $coin = array_shift($args);
        if (!isset($coin) or !is_numeric($coin)) {
            $sender->sendMessage(Coin::PREFIX . "코인은 숫자여야 합니다.");
            return true;
        }
        Coin::setCoin($name, (float)$coin);
        $sender->sendMessage(Coin::PREFIX . $name . " 님의 코인을 " . $coin . " 으로 설정하였습니다.");
        return true;
    }
}