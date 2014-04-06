<?php
Class StillBeautyCart {
	private $items = array();

	function __construct() { }

	public function productInCart($product) {
		if (count($this->items)) {
			for($i = 0; $i < count($this->items); ++$i) {
				if ($this->items[$i]['product']['id'] == $product['id']) return $i;
			}
		}

		return -1;
	}

	public function getProductImage($id) {

		$start = get_page_by_title('Products');
		$cat = get_pages(array(
					'sort_column' => 'menu_order',
					'child_of' => $start->ID,
					'parent' => $start->ID
				));

		foreach ($cat as $c) {
			$products = get_pages(array(
					'sort_column' => 'menu_order',
					'child_of' => $c->ID,
					'parent' => $c->ID
				));

			foreach($products as $p) {
				$product_id = get_post_meta( $p->ID, "product_id", true );
				if ($product_id == $id) {
					$image = get_the_post_thumbnail( $p->ID, 'full' );
					return $image;
				}
			}
		}

		return NULL;
	}

	public function push($product) {
		if (!empty($product) && $this->productInCart($product) >= 0) {
			$this->items[$this->productInCart($product)]['quantity']++;
		} else {
			$product['src'] = $this->getProductImage($product['id']);
			array_push($this->items, 
					   array('quantity' => 1, 'product' => $product));
		}
	} 

	public function reset() {
		array_splice($this->items, 0, count($this->items));
	}

	public function getItems() {
		return $this->items;
	}

	public function getTotal() {
		$sum = 0.0;

		foreach ($this->items as $item) {
			$sum += $item['quantity'] * $item['product']['price'];
		}

		return '$' . number_format($sum, 2);
	}

	public function getTotalItems() {
		$n = 0.0;

		foreach ($this->items as $item) {
			$n += $item['quantity'];
		}

		return $n;
	}

	public function rm($id) {
		for($i=0; $i < count($this->items); ++$i) {
			if ($id == $this->items[$i]['product']['id']) {
				array_splice($this->items, $i, 1);
				break;
			}
		}
	}
}
?>