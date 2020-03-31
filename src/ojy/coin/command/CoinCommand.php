<?php

namespace ojy\coin\command;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;

abstract class CoinCommand extends PluginCommand
{

    final public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        return $this->_execute($sender, $args);
    }

    abstract public function _execute(CommandSender $sender, array $args): bool;
}