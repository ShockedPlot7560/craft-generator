<?php

declare(strict_types=1);

namespace ShockedPlot7560\craftGenerator;

use GdImage;
use function imagecreatefrompng;

class Item {
	private $iconUrl;
	private $name;
	private $slot;

	public function __construct(string $url, string $name){
		$this->iconUrl = $url;
		$this->name = $name;
	}

	public function getIconUrl() : string{
		return $this->iconUrl;
	}

	public function getImage() : GdImage{
		return imagecreatefrompng($this->getIconUrl());
	}

	public function getName() : string{
		return $this->name;
	}

    public function setSlot(int $slot): void{
		$this->slot = $slot;
    }
}