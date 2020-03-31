<?php

namespace ojy\coin;

use lang\Lang;
use ojy\coin\command\MyCoinCommand;
use ojy\coin\command\PayCoinCommand;
use ojy\coin\command\SeeCoinCommand;
use ojy\coin\command\SetCoinCommand;
use ojy\coin\command\TopCoinCommand;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\Config;

class Coin extends PluginBase
{

    public const PREFIX = '§l§b[Coin] §r§7';

    /** @var array */
    public static $coin;
    /** @var self */
    private static $i;
    /** @var Config */
    public static $setting;

    public const COIN_PREFIX = "coin_";

    public function onLoad()
    {
        self::$i = $this;
    }

    public static function getInstance(): self
    {
        return self::$i;
    }

    public function onEnable()
    {
        file_exists($this->getDataFolder() . "Coin.json") ? self::$coin = json_decode(file_get_contents($this->getDataFolder() . "Coin.json"), true) ?? [] : self::$coin = [];
        self::$setting = new Config($this->getDataFolder() . "Setting.yml", Config::YAML, ["default-coin" => 10]);
        new EventListener();
        Lang::registerLang(self::COIN_PREFIX . "see_타겟", "코인을 볼 플레이어의 이름을 입력해주세요.", "Please enter the name of the player who will see the coin.");
        Lang::registerLang(self::COIN_PREFIX . "타겟_미스", "해당 이름의 플레이어는 서버에 접속한 적이 없습니다.", "The player with that name has never joined the server.");
        Lang::registerLang(self::COIN_PREFIX . "see_코인", " 의 코인: ", "'s coin: ");
        Lang::registerLang(self::COIN_PREFIX . "코인순위", "========== [ 코인 순위 ] ==========", "========== [ TopCoin ] ==========");
        Lang::registerLang(self::COIN_PREFIX . "pay_타겟", "코인을 지불할 플레이어의 이름을 입력해주세요.", "Please enter the name of the player who will pay the coin.");
        Lang::registerLang(self::COIN_PREFIX . "pay_코인", "보낼 코인의 양은 숫자여야 합니다.", "The coin number must be number.");
        Lang::registerLang(self::COIN_PREFIX . "코인부족", "코인이 부족합니다.", "Your coin is not enough.");
        Lang::registerLang(self::COIN_PREFIX . "지불_성공", "%s님에게 %s코인을 지불하였습니다.", "You paid to {%s} for {%s} coin.");

        foreach ([
                     PayCoinCommand::class,
                     SeeCoinCommand::class,
                     SetCoinCommand::class,
                     TopCoinCommand::class,
                     MyCoinCommand::class
                 ] as $class) {
            $this->getServer()->getCommandMap()->register("coin", new $class($this));
        }

        $this->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function (int $currentTick): void {
            self::save();
        }), 20 * 60 * 10, 20 * 60 * 10);
    }

    public static function getRank(Player $player): int
    {
        $playerName = strtolower($player->getName());
        arsort(self::$coin);
        return array_search($playerName, array_keys(self::$coin)) + 1;
    }

    public static function koreanWonFormat($money): string
    {
        $str = '';
        $elements = [];
        if ($money >= 1000000000000) {
            $elements[] = floor($money / 1000000000000) . "조";
            $money %= 1000000000000;
        }
        if ($money >= 100000000) {
            $elements[] = floor($money / 100000000) . "억";
            $money %= 100000000;
        }
        if ($money >= 10000) {
            $elements[] = floor($money / 10000) . "만";
            $money %= 10000;
        }
        if (count($elements) == 0 || $money > 0) {
            $elements[] = $money;
        }
        return implode(" ", $elements);
    }

    public static function save()
    {
        Server::getInstance()->getLogger()->info("코인 데이터 저장");
        file_put_contents(self::$i->getDataFolder() . "Coin.json", json_encode(self::$coin));
    }

    public function onDisable()
    {
        self::save();
    }

    public static function getCoin($player): ?float
    {
        $player = ($player instanceof Player) ? strtolower($player->getName()) : strtolower($player);
        $coin = self::$coin[$player] ?? null;
        if ($coin !== null)
            $coin = round($coin, 1);
        return $coin;
    }

    public static function addCoin($player, float $coin): bool
    {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        if (!isset(self::$coin[$player])) {
            return false;
        }
        self::$coin[$player] += $coin;
        return true;
    }

    public static function reduceCoin($player, float $coin): bool
    {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        if (!isset(self::$coin[$player])) {
            return false;
        }
        self::$coin[$player] -= $coin;
        return true;
    }

    public static function setCoin($player, float $coin): bool
    {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        if (!isset(self::$coin[$player])) {
            return false;
        }
        self::$coin[$player] = $coin;
        return true;
    }

    /**
     * @return int[]
     */
    public static function getAll(): array
    {
        arsort(self::$coin);
        return self::$coin;
    }
}