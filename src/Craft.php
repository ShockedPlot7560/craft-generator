<?php

declare(strict_types=1);

namespace ShockedPlot7560\craftGenerator;

use Intervention\Image\ImageManager;
use InvalidArgumentException;
use function count;
use function hexdec;
use function imagealphablending;
use function imagecolorallocate;
use function imagecolorallocatealpha;
use function imagecopyresampled;
use function imagecreate;
use function imagecreatetruecolor;
use function imagefill;
use function imagefilledrectangle;
use function imagepng;
use function imagesavealpha;
use function imagesx;
use function imagesy;

class Craft {

	private array $CRAFT_SIZE;
	private const CRAFT_SLOT_SIZE = 15;
	public const CRAFT_SLOT_RESULT = 14;

	/** @var Item[] */
	private array $items = [];
	private int $type;

	private array $slots = [];

	private $options ;

	public function __construct(int $type, array $options = []){
		$this->type = $type;
		$int = 0;
		$buffer = [];
		for ($i = 1; $i <= self::CRAFT_SLOT_SIZE; $i++) {
			$buffer[] = $i;
			if($int >= 2){
				$int = 0;
				$this->slots[] = $buffer;
				$buffer = [];
			}else{
				$int++;
			}
		}

		$this->options = $options;
		if(empty($options["background-color"])){
			$this->options["background-color"] = [
				hexdec("2E"), hexdec("2E"), hexdec("2E")
			];
		}
		if(empty($options["craft-background"])){
			$this->options["craft-background"] = [
				hexdec("3E"), hexdec("3E"), hexdec("3E")
			];
		}
		if(empty($options["size_base"])){
			$this->options["size_base"] = [
				16, 16
			];
		}
		$this->CRAFT_SIZE = [
			$this->options["size_base"][0] * 4 + $this->options["size_base"][0] / 4 * 2 + $this->options["size_base"][0] * 2 + $this->options["size_base"][0],
			$this->options["size_base"][1] * 3 + $this->options["size_base"][1] / 4 * 2 + $this->options["size_base"][1]
		];
	}

	public function addItem(Item $item, int $slot) : void{
		$this->items[$slot] = $item;
	}

	public function getType() : int {
		return $this->type;
	}

	public function generate($path) : void{
		$this->addItem(new Item(__DIR__ . "/arrow.png"), 11);
		$canva = imagecreate($this->CRAFT_SIZE[0], $this->CRAFT_SIZE[1]);
		imagecolorallocate($canva, $this->options["background-color"][0], $this->options["background-color"][1], $this->options["background-color"][2]);
		$caseColor = imagecolorallocate($canva, $this->options["craft-background"][0], $this->options["craft-background"][1], $this->options["craft-background"][2]);

		for ($i = 1; $i <= 9; $i++) {
			$coordonate = $this->getSlotFill($i);
			imagefilledrectangle($canva, $coordonate[0], $coordonate[1], $coordonate[2], $coordonate[3], $caseColor);
		}

		$resultCo = $this->getSlotFill(self::CRAFT_SLOT_RESULT);
		imagefilledrectangle($canva, $resultCo[0], $resultCo[1], $resultCo[2], $resultCo[3], $caseColor);
		imagepng($canva, $path);

		$manager = new ImageManager([
			"driver" => "gd"
		]);

		$image = $manager->make($path);
		foreach ($this->items as $slot => $item) {
			$coordonate = $this->getSlotFill($slot);
			$src = $item->getImage();
			$resized = imagecreatetruecolor($this->options["size_base"][0], $this->options["size_base"][1]);

			imagealphablending($resized, true);
			imagesavealpha($resized, true);

			$tranparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
			imagefill($resized, 0, 0, $tranparent);
			imagecopyresampled($resized, $src, 0, 0, 0, 0, $this->options["size_base"][0], $this->options["size_base"][1], imagesx($src), imagesy($src));
			$image->insert($resized, 'top-left', $coordonate[0], $coordonate[1]);
		}
		$image->save($path);
	}

	public function export(string $path) : void{
		$this->generate($path);
		//imagepng($this->generate(), $path);
	}

	/**
	 * @return int[] An array with the according coordonate
	 * 		- [0] The start x
	 * 		- [1] The start y
	 * 		- [2] The end x
	 * 		- [3] The end y
	 */
	public function getSlotFill(int $slot) : array{
		for ($i = 0; $i < count($this->slots); $i++) {
			for ($j = 0; $j < count($this->slots[$i]); $j++) {
				if($this->slots[$i][$j] == $slot){
					return [
						$this->options["size_base"][0] / 2 + ($this->options["size_base"][0] + $this->options["size_base"][0] / 4) * $i,
						$this->options["size_base"][1] / 2 + ($this->options["size_base"][1] + $this->options["size_base"][1] / 4) * $j,
						$this->options["size_base"][0] / 2 + $this->options["size_base"][0] + ($this->options["size_base"][0] + $this->options["size_base"][0] / 4) * $i - 1,
						$this->options["size_base"][1] / 2 + $this->options["size_base"][1] + ($this->options["size_base"][1] + $this->options["size_base"][1] / 4) * $j - 1
					];
				}
			}
		}
		throw new InvalidArgumentException("Slot is index of bound 1-9, $slot given");
	}
}
