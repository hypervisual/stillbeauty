<?php

Class StillBeautyCatalogue {

	private $category = array(
		array('id' => 1, 'name' => 'Candle'),
		array('id' => 2, 'name' => 'Herbal Tea'),
		array('id' => 3, 'name' => 'Massage Oil')
	);

	private $products = array(
			array('id' => 1, 'cat' => 1, 'src' => '', 'type' => 'Candle', 'name' => 'Calm', 'price' => '22.0'),
			array('id' => 2, 'cat' => 1, 'src' => '', 'type' => 'Candle', 'name' => 'Invigorate', 'price' => '22.0'),
			array('id' => 3, 'cat' => 1, 'src' => '', 'type' => 'Candle', 'name' => 'Unwind', 'price' => '22.0'),
			array('id' => 4, 'cat' => 1, 'src' => '', 'type' => 'Candle', 'name' => 'Expecting', 'price' => '22.0'),

			array('id' => 5, 'cat' => 2, 'src' => '', 'type' => 'Herbal Tea', 'name' => 'Calm', 'price' => '12.0'),
			array('id' => 6, 'cat' => 2, 'src' => '', 'type' => 'Herbal Tea', 'name' => 'Invigorate', 'price' => '12.0'),
			array('id' => 7, 'cat' => 2, 'src' => '', 'type' => 'Herbal Tea', 'name' => 'Unwind', 'price' => '12.0'),
			array('id' => 8, 'cat' => 2, 'src' => '', 'type' => 'Herbal Tea', 'name' => 'Expecting', 'price' => '12.0'),

			array('id' => 9, 'cat' => 3, 'src' => '', 'type' => 'Massage Oil', 'name' => 'Calm', 'price' => '22.0'),
			array('id' => 10, 'cat' => 3, 'src' => '', 'type' => 'Massage Oil', 'name' => 'Invigorate', 'price' => '22.0'),
			array('id' => 11, 'cat' => 3, 'src' => '', 'type' => 'Massage Oil', 'name' => 'Unwind', 'price' => '22.0'),
			array('id' => 12, 'cat' => 3, 'src' => '', 'type' => 'Massage Oil', 'name' => 'Expecting', 'price' => '22.0')

	);

	function __construct() { }

	public function getProduct($id) {
		foreach($this->products as $product) {
			if ($product['id'] == $id) {
				return $product;
			}
		}
		return NULL;
	}


}

?>