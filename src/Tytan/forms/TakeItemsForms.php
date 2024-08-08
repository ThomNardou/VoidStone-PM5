<?php

namespace Tytan\forms;

use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use Tytan\libs\Vecnavium\FormsUI\SimpleForm;
use Tytan\Main;

class TakeItemsForms
{
	public static function OpenItemForms(Player $player, Item $item)
	{
		$nbt = $item->getNamedTag();
		$itemsArray = [];

		foreach ($nbt as $key => $value) {
			if (in_array($key, Main::$configData["itemsStockable"])) {
				$itemsArray[] = ["item" => $key, "amount" => $value->getValue()];
			}
		}


		$form = new SimpleForm(function (Player $player, $data) use ($itemsArray, $item) {
			if ($data === null) {
				return;
			}

			$idLast = $item->getNamedTag()->getString("token","null0");
			$idNext = $player->getInventory()->getItemInHand()->getNamedTag()->getString("token","null1");

			if ($idLast != $idNext) return;

			if ($data == count($itemsArray)) {
				foreach ($itemsArray as $itemToGive) {
					$itemObject = StringToItemParser::getInstance()->parse($itemToGive["item"]);

					if ($player->getInventory()->canAddItem($itemObject)) {
						$player->getInventory()->addItem($itemObject->setCount($itemToGive["amount"]));
					}
					else {
						$player->getWorld()->dropItem($player->getPosition(), $itemObject->setCount($itemToGive["amount"]));
					}

				}
				$player->sendMessage(Main::$configData["takeOneItemMessage"]);
				$newItem = $player->getInventory()->getItemInHand()->clearNamedTag();
				$player->getInventory()->setItemInHand($newItem);

			}
			else {
				$itemToGive = $itemsArray[$data];
				$itemObject = StringToItemParser::getInstance()->parse($itemToGive["item"]);
				$voidStoneItemPlayer = $player->getInventory()->getItemInHand();

				if ($player->getInventory()->canAddItem($itemObject)) {
					$player->getInventory()->addItem($itemObject->setCount($itemToGive["amount"]));

					$voidStoneItemPlayer->getNamedTag()->removeTag($itemToGive["item"]);

				}
				else {
					$player->getWorld()->dropItem($player->getPosition(), $itemObject->setCount($itemToGive["amount"]));
				}

				$lore = [];

				foreach ($voidStoneItemPlayer->getNamedTag() as $key => $value) {
					if (in_array($key, Main::$configData["itemsStockable"])) {
						$lore[] = "§r§7$key: §6x" . $value->getValue() . "§r";
					}
				}

				$voidStoneItemPlayer->setLore($lore);

				$player->getInventory()->setItemInHand($voidStoneItemPlayer);

				$message = str_replace(["{item}", "{amount}"], [$itemToGive["item"], $itemToGive["amount"]], Main::$configData["takeOneItemMessage"]);
				$player->sendMessage($message);
			}

		});


		$form->setTitle(Main::$configData["formTitle"]);

		if (count($itemsArray) == 0) {
			$form->setContent("§cVoidStone is empty");
		}
		else {

			$form->setContent("Chose items to take from VoidStone");
			foreach ($itemsArray as $item) {
				$form->addButton("§e" . $item["item"] . "§r\n§6x" . $item["amount"]);
			}
			$form->addButton(Main::$configData["AllItemsTakenButton"]);

		}

		$player->sendForm($form);
	}
}