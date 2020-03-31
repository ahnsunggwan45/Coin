<?php

namespace ojy\coin\command;

use lang\Lang;
use ojy\coin\Coin;
use ojy\money\MoneyReporter;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;

class PayCoinCommand extends CoinCommand
{

    public function __construct(Coin $plugin)
    {
        parent::__construct("지불", $plugin);
        $this->setUsage("/지불 [닉네임] [코인]");
        $this->setAliases(["pay", "입금"]);
    }

    public function _execute(CommandSender $sender, array $args): bool
    {
        if (!$sender instanceof Player) return true;
        if (!isset($args[0]) || !isset($args[1])) {
            $sender->sendMessage(Coin::PREFIX . $this->getUsage());
            return true;
        }
        $pay = round((float)array_pop($args), 1);
        $name = implode(" ", $args);
        if (($player = Server::getInstance()->getPlayer($name)) !== null)
            $name = $player->getName();
        if (strtolower($name) === strtolower($sender->getName())) {
            $sender->sendMessage(Coin::PREFIX . "자신에게 입금할 수는 없습니다.");
            return true;
        }
        $coin = Coin::getCoin($name);
        if ($coin === null) {
            $sender->sendMessage(Coin::PREFIX . Lang::translate(Coin::COIN_PREFIX . "타겟_미스", Lang::getLang($sender)));
            return true;
        }
        if (!isset($pay) or !is_numeric($pay)) {
            $sender->sendMessage(Coin::PREFIX . Lang::translate(Coin::COIN_PREFIX . "pay_코인", Lang::getLang($sender)));
            return true;
        }
        if ($pay < 0) {
            $sender->sendMessage(Coin::PREFIX . "0보다 큰 수를 입력해주세요.");
            return true;
        }
        $myCoin = Coin::getCoin($sender);
        if ($myCoin < $pay) {
            $sender->sendMessage(Coin::PREFIX . Lang::translate(Coin::COIN_PREFIX . "코인부족", Lang::getLang($sender)));
            return true;
        }
        if (Server::getInstance()->getPluginManager()->getPlugin("MoneyReporter") !== null)
            MoneyReporter::log($sender->getName(), $name, $pay);
        Coin::reduceCoin($sender, $pay);
        Coin::addCoin($name, ($pay * 99 / 100));
        if ($player !== null)
            $player->sendMessage(Coin::PREFIX . "{$sender->getName()}님이 {$pay} 코인을 입금했습니다.");
        $sender->sendMessage(Coin::PREFIX . sprintf(Lang::translate(Coin::COIN_PREFIX . "지불_성공", Lang::getLang($sender)), $name, $pay));
        return true;
    }
}