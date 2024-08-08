<?php

namespace Tytan\Event;

use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\Listener;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use Tytan\Main;

class OnGetItem implements Listener
{
	public function PlayerGetItem(EntityItemPickupEvent $event): void {
		$entity = $event->getEntity();
		$itemPicked = $event->getItem();

		if ($entity instanceof Player) {

			$item = Main::getInstance()->getVoidStoneItem();
			$lastItem = $entity->getInventory()->getItem(8);

			$aliases = StringToItemParser::getInstance()->lookupAliases($itemPicked);

			count($aliases) == 1 ? $pickedItemAlias = $aliases[0] : $pickedItemAlias = $aliases[1];

			if ($item->equals($lastItem, false, false) && in_array($pickedItemAlias, Main::$configData["itemsStockable"])) {

				if ($lastItem->getCount() > 1) {
					$entity->sendMessage(Main::$configData["stackableItemsMessage"]);
					return;
				}

				$nbt = $lastItem->getNamedTag();
				$token = $nbt->getString("token", bin2hex(random_bytes(16)));
				$count = $nbt->getInt($pickedItemAlias, 0);
				$nbt->setInt($pickedItemAlias, $count + $itemPicked->getCount());
				$nbt->setString("token", $token);
				$lastItem->setNamedTag($nbt);

				$lore = [];

				foreach ($lastItem->getNamedTag() as $key => $value) {
					if (in_array($key, Main::$configData["itemsStockable"])) {
						$lore[] = "§r§7$key: §6x" . $value->getValue() . "§r";
					}
				}

				$lastItem->setLore($lore);
				$entity->getInventory()->setItem(8, $lastItem);


				$event->setItem(VanillaItems::AIR());

			}



		}
	}

}