<?php

class Pagination {

	public $items_pp;
	public $total_items;
	public $total_pages;
	public $cur_page;
	public $mid_range;
	public $low;
	public $high;
	public $limit;
	public $return;
	public $default_pp;	

	public $max_pages;
	public $dal;
	
	public $searchLink;
	public $isSearch;

	public function __construct($params, $default_pp) {
		$this->params = $params;
		$this->dal = new DataAccessLayer();

		$this->default_pp = $default_pp;

		$this->items_pp = (isset($params['items'])) ? $params['items'] : $this->default_pp;

		$this->items = $params['items'];
		$this->phase = $params['phase'];
		$this->p = $params['offset'];
		$this->base = $params['baseurl'];
		$this->page = $params['page'];
		$this->max_pages = 9;
		
		if(isset($params['searchLink'])==true){
			$this->searchLink = $params['searchLink'];
			$this->isSearch = true;
		}
		else{
			if(isset($params['apps'])==true){
				$this->searchLink = '&apps=' . $params['apps'];
			}
			else
				$this->searchLink = "";
			$this->isSearch = false;
		}
	}

	public function total_items() {
		if (!isset( $this->total_items ) ) {
			if($this->isSearch == false)
				$this->total_items = count( $this->dal->gridData(array_merge( $this->params, array( 'items' => 'all' ) ) ) ) ;
			else
				$this->total_items = count( $this->dal->search(array_merge( $this->params, array( 'items' => '99999' ),array( 'offset' => '0' )  ) ) );
		}
		
		return $this->total_items;
	}

	public function total_pages() {
		if ( !isset( $this->total_pages ) && $this->items!=null) {
			$this->total_pages = ceil($this->total_items() / $this->items );
		}
		else{
			$this->total_pages = 1;
		}
		return $this->total_pages;
	}

	public function render() {
		$total_items = $this->total_items();
		$total_pages = $this->total_pages();

		if ( $this->items == 'all' ) {
			$this->items_pp = $total_items;
			$total_pages = 1;
		} else {
			if($this->items_pp!=null){
				$total_pages = ceil($total_items / $this->items_pp);
			}
			else
				$total_pages = 1;
		}
		
		if ( $this->items != 'all' ) {

        	        print "<ul class='pagerlinks'>";

			if ( $this->p > 0 ) {
				print "<li><a href='" . $this->base . $this->page . "?items=" . $this->items_pp . "&p=" . ($this->p - 1) . $this->searchLink . "' id='prev'>Prev</a></li>";
			}

			$start = 1;
			$maxpages = 9;
		
			if ( $total_pages > 9 ) {
				$diff = $total_pages - 9;
				$start = max($this->p - 5, 1);
				$maxpages = max($this->p + 5, 9);
				if ( $maxpages > $total_pages ) {
					$maxpages = $total_pages;
					$start = $maxpages - 9;
				}
			}
			else{
				$maxpages = min($total_pages, 9);
			}

			//$j = 0;
			for ( $i = $start; $i <= $maxpages; $i++ ) {
				//if ( $j <= 9 ) { 
        				//print "<li><a href='" . $this->base . $this->page . "?items=" . $this->items_pp . $this->searchLink . "&p=$j'>$i</a></li>";
						print "<li><a href='" . $this->base . $this->page . "?items=" . $this->items_pp . $this->searchLink . "&p=" . ($i-1) . "'>$i</a></li>";
				//}
				//$j++;
			} 

			if ( $this->p + 1 < $total_pages ) {
				print "<li><a href='" . $this->base . $this->page . "?items=" . $this->items_pp . "&p=" . ($this->p + 1) . $this->searchLink. "' id='next'>Next</a></li>";
			}

			print "<li><a href='" . $this->base . $this->page . "?items=" . $total_items . $this->searchLink. "' id='all'>All</a></li>";
			print "</ul>";
		}
	}
}
