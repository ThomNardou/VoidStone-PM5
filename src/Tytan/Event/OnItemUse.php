<?php

namespace Tytan\Event;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use Tytan\forms\TakeItemsForms;
use Tytan\Main;

class OnItemUse implements Listener
{
	public function PlayerUseItem(PlayerItemUseEvent $event): void {
		$player = $event->getPlayer();
		$item = $event->getItem();

		if ($item->equals(Main::getInstance()->getVoidStoneItem(), false, false)) {
			TakeItemsForms::OpenItemForms($player, $item);
		}
	}
}