<?php

namespace Tytan;

use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\Server;
use Tytan\Event\OnGetItem;
use Tytan\Event\OnItemUse;

class Main extends PluginBase
{
	use SingletonTrait;

	public static $configData = [];
	protected function onEnable(): void
	{
		self::setInstance($this);

		$this->saveDefaultConfig();
		self::$configData = $this->getConfig()->getAll();

		$this->registerEvent();

		Server::getInstance()->getLogger()->info("Plugin voidStone enabled");
	}

	protected function onDisable(): void
	{
		Server::getInstance()->getLogger()->info("Plugin voidStone disabled");
	}

	public function registerEvent(): void
	{
		Server::getInstance()->getPluginManager()->registerEvents(new OnGetItem(), $this);
		Server::getInstance()->getPluginManager()->registerEvents(new OnItemUse(), $this);
	}

	public function getVoidStoneItem(): Item
	{
		return StringToItemParser::getInstance()->parse(self::$configData["item"]);
	}

}