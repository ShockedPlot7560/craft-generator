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

	private const CRAFT_SIZE = [
		16 * 4 + 4 * 2 + 32 + 16,
		16 * 3 + 4 * 2 + 16
	];
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

		$this->options["craft-background"] = $options;
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
	}

	public function addItem(Item $item, int $slot) : void{
		$this->items[$slot] = $item;
	}

	public function getType() : int {
		return $this->type;
	}

	public function generate($path) : void{

		$canva = imagecreate(self::CRAFT_SIZE[0], self::CRAFT_SIZE[1]);
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
			$resized = imagecreatetruecolor(16, 16);

			imagealphablending($resized, true);
			imagesavealpha($resized, true);

			$tranparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
			imagefill($resized, 0, 0, $tranparent);
			imagecopyresampled($resized, $src, 0, 0, 0, 0, 16, 16, imagesx($src), imagesy($src));
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
						8 + (16 + 4) * $i,
						8 + (16 + 4) * $j,
						8 + 16 + (16 + 4) * $i - 1,
						8 + 16 + (16 + 4) * $j - 1
					];
				}
			}
		}
		throw new InvalidArgumentException("Slot is index of bound 1-9, $slot given");
	}
}