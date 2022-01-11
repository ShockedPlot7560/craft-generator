<?php

declare(strict_types=1);

use ShockedPlot7560\craftGenerator\Craft;
use ShockedPlot7560\craftGenerator\Item;

require dirname(__DIR__) . "/vendor/autoload.php";

$craft = new Craft(1, [
	"size_base" => [
		1080, 1080

	]
]);
$craft->addItem(new Item(__DIR__ . "/amethyst_shard.png", "Amethyst"), 1);
$craft->addItem(new Item(__DIR__ . "/apple.png", "Apply"), 9);
$craft->addItem(new Item(__DIR__ . "/bamboo.png", "Bamboo"), Craft::CRAFT_SLOT_RESULT);
$craft->addItem(new Item(__DIR__ . "/blast_furnace_top.png", "Bamboo"), 4);
$craft->export(__DIR__ . "/test.png");