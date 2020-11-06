<?php

declare(strict_types=1);

namespace MonoAdrian23\MonoRanks;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase{

	public function onEnable() {
		$this->saveDefaultConfig();

		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
	}
}
