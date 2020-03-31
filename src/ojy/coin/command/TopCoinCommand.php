<?php

namespace ojy\coin\command;

use lang\Lang;
use ojy\coin\Coin;
use pocketmine\command\CommandSender;

class TopCoinCommand extends CoinCommand
{

    public function __construct(Coin $plugin)
    {
        parent::__construct("코인순위", $plugin);
        $this->setAliases(["topcoin", "돈순위"]);
    }

    public function _execute(CommandSender $sender, array $args): bool
    {
        //if (!$sender instanceof Player) return true;
        $coins = Coin::getAll();
        $maxPage = ceil(count($coins) / 5);
        $page = (isset($args[0]) && is_numeric($args[0]) && (int)$args[0] > 0) ? (int)$args[0] : 1;
        if ($page > $maxPage)
            $page = $maxPage;
        $index1 = $page * 5 - 5;
        $index2 = $page * 5 - 1;
        $count = 0;
        $sender->sendMessage("========[코인 순위({$page}/{$maxPage})]========");
        foreach ($coins as $playerName => $coin) {
            if ($index1 <= $count && $index2 >= $count) {
                $rate = $count + 1;
                $coin = Coin::koreanWonFormat($coin);
                $sender->sendMessage("§l§b[{$rate}위] §r§7" . $playerName . " > " . $coin . " 코인");
            }
            ++$count;
        }
        return true;
    }
}