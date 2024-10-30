jQuery(document).ready(function($){
	var $grid = $('.grid').isotope({
	    itemSelector: '.element-item',
	    layoutMode: 'fitRows'
  	});
	  // filter functions
	var filterFns = {};
	  // bind filter on select change
	$('.filterPort').on( 'click', function() {
	    // get filter value from option value
	    var filterValue = $(this).attr('data-filter');
	    // $grid.isotope({ filter: filterValue });
	    // var filterValue = this.value;
	    // use filterFn if matches value
	    filterValue = filterFns[ filterValue ] || filterValue;
	    $grid.isotope({ filter: filterValue });
	});
});