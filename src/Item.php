<?php

declare(strict_types=1);

namespace ShockedPlot7560\craftGenerator;

use GdImage;
use function imagecreatefrompng;

class Item {
	private $iconUrl;

	public function __construct(string $url){
		$this->iconUrl = $url;
	}

	public function getIconUrl() : string{
		return $this->iconUrl;
	}

	public function getImage() : GdImage{
		return imagecreatefrompng($this->getIconUrl());
	}
}