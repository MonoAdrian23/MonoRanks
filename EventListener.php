<?php

declare(strict_types=1);

namespace MonoAdrian23\MonoRanks;

use _64FF00\PurePerms\PurePerms;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat;

class EventListener implements Listener {

	/** @var PurePerms */
	private $pp;
	/** @var Main  */
	private $plugin;

	private $cd = [];

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		$this->pp = $plugin->getServer()->getPluginManager()->getPlugin("PurePerms");
	}

	public function onInteract(PlayerInteractEvent $event): void {
		$player = $event->getPlayer();
		$name = $player->getName();
		if(isset($this->cd[$name])){
			if(time() - $this->cd[$name] < 1) {
				return;
			} else $this->cd[$name] = time();
		} else $this->cd[$name] = time();
		$block = $event->getBlock();
		if(($tile = $block->getLevel()->getTile($block)) && $tile instanceof Sign){
			$line1 = TextFormat::clean($tile->getLine(0));
			$price = intval(TextFormat::clean($tile->getLine(1)));
			$line3 = $tile->getLine(2);
			if($line1 === "[RankShop]" && $group = $this->pp->getGroup($line3)) {
				if(EconomyAPI::getInstance()->myMoney($player) >= $price){
					$this->pp->setGroup($player, $group);
					EconomyAPI::getInstance()->reduceMoney($player, $price);
					$player->sendMessage(str_replace(["{rank}", "{price}"], [$group->getName(), $price], $this->plugin->getConfig()->get("successfully_bought")));
				} else $player->sendMessage($this->plugin->getConfig()->get("not_enough_money"));
			}
		}
	}

	public function onSignChange(SignChangeEvent $event): void {
		$player = $event->getPlayer();

		if($player->isOp()) {
			$line1 = TextFormat::clean($event->getLine(0));
			$line2 = TextFormat::clean($event->getLine(1));
			$line3 = $event->getLine(2);

			if($line1 === "[RankShop]") {
				if($line3 !== "") {
					if($group = $this->pp->getGroup($line3)) {
						$price = intval($line2);
						$player->sendMessage("Rankshop successfully created, rank: " . $group->getName() . ", price: $price");
					}
				}
			}
		}
	}

}