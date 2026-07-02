<?php
namespace Opencart\System\Library\Template;

class OpenCartExtension extends \Twig\Extension\AbstractExtension {
	public function getFilters(): array {
		return [
			new \Twig\TwigFilter('replace_first', function($str, $search = '', $replace = '') {
				if (!$str || !$search) {
					return $str;
				}
				$pos = strpos((string)$str, (string)$search);
				if ($pos !== false) {
					return substr_replace((string)$str, (string)$replace, $pos, strlen((string)$search));
				}
				return $str;
			}),
			new \Twig\TwigFilter('replace', function($str, $search = '', $replace = '') {
				if (!$str || !$search) {
					return $str;
				}
				return str_replace((string)$search, (string)$replace, (string)$str);
			}),
			new \Twig\TwigFilter('divide', function($value, $divisor = 1) {
				if ((int)$divisor === 0) {
					return 0;
				}
				return (int)$value / (int)$divisor;
			}),
			new \Twig\TwigFilter('divided', function($value, $divisor = 1) {
				if ((int)$divisor === 0) {
					return 0;
				}
				return (int)$value / (int)$divisor;
			}),
			new \Twig\TwigFilter('batch', function($items, $size = 1) {
				if (!is_array($items)) {
					return [];
				}
				$batches = [];
				$batch = [];
				foreach ($items as $item) {
					$batch[] = $item;
					if (count($batch) === (int)$size) {
						$batches[] = $batch;
						$batch = [];
					}
				}
				if (!empty($batch)) {
					$batches[] = $batch;
				}
				return $batches;
			}),
			new \Twig\TwigFilter('slice', function($items, $offset = 0, $length = null) {
				if (!is_array($items)) {
					return [];
				}
				return array_slice($items, (int)$offset, $length !== null ? (int)$length : null);
			}),
		];
	}
}
