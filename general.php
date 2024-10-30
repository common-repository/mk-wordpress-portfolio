<?php
class MK_Portfolio{
	/*function mk_activate(){
		return 1;
	}*/
	function checkPostType(){
		$args = array(
		   'public'   => true,
		   '_builtin' => true
		);
		$posttypes = get_post_types($args);
		if(!in_array("portfolio", $posttypes)){
			$this->portfolioPosts();
		}
		$this->portfolioTaxonomy();
	}

	function portfolioTaxonomy(){
		$labels = array(
			'name'              => __( 'Category of work'),
			'singular_name'     => __( 'Category of work'),
			'search_items'      => __( 'Search Category' ),
			'all_items'         => __( 'All Categories' ),
			'parent_item'       => __( 'Parent Category' ),
			'parent_item_colon' => __( 'Parent Category:' ),
			'edit_item'         => __( 'Edit CategoryOfWork' ),
			'update_item'       => __( 'Update CategoryOfWork' ),
			'add_new_item'      => __( 'Add New CategoryOfWork' ),
			'new_item_name'     => __( 'New CategoryOfWork Name' ),
			'menu_name'         => __( 'CategoryOfWork' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'categoryofwork' ),
		);

		register_taxonomy( 'categoryofwork', array( 'portfolio' ), $args );
	}

	function addIsotopQuery(){
		// echo MK_PORTFOLIO_PATH;
		// exit;
		wp_register_script('isotop-jq',MK_PORTFOLIO_PATH.'js/isotope.pkgd.js');
		wp_register_script('sorting-jq',MK_PORTFOLIO_PATH.'js/sorting.js');
		wp_register_style( 'layout-style', MK_PORTFOLIO_PATH.'css/layout.css' );
	}

	function portfolioPosts() {
		$labels = array(
			'name'               => __( 'Portfolios' ),
			'singular_name'      => __( 'Portfolio' ),
			'menu_name'          => __( 'Portfolios' ),
			'name_admin_bar'     => __( 'Portfolio' ),
			'add_new'            => __( 'Add New' ),
			'add_new_item'       => __( 'Add New Portfolio' ),
			'new_item'           => __( 'New Portfolio' ),
			'edit_item'          => __( 'Edit Portfolio' ),
			'view_item'          => __( 'View Portfolio' ),
			'all_items'          => __( 'All Portfolios' ),
			'search_items'       => __( 'Search Portfolios' ),
			'parent_item_colon'  => __( 'Parent Portfolios:' ),
			'not_found'          => __( 'No Portfolio found.' ),
			'not_found_in_trash' => __( 'No Portfolio found in Trash.' )
		);

		$args = array(
			'labels'             => $labels,
	                'description'        => __( 'Description.' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'portfolio' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'comments' )
		);

		register_post_type( 'portfolio', $args );
	}

	function portfolioMeta(){
		add_meta_box('portfolio-meta','Portfolio URL',array($this,'portfolioURL'),'portfolio','normal','high');
	}

	function portfolioURL($post){
		wp_nonce_field( 'portfolio_nonce', 'portfolio_meta' );
		$portfolioLabel = (get_post_meta($post->ID,'portfolioURL',true)) ? get_post_meta($post->ID,'portfolioURL',true) : "";
		?>
		<p>
	        <label for="portfolioLabel">Portfolio URL:</label>
	        <input type="text" name="portfolioLabel" id="portfolioLabel" value="<?php echo $portfolioLabel; ?>" />
	    </p>
		<?php
	}

	function savePortfolioURL($post_id){
		
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // if our nonce isn't there, or we can't verify it, bail
    	if( !isset( $_POST['portfolio_meta'] ) || !wp_verify_nonce( $_POST['portfolio_meta'], 'portfolio_nonce' ) ) return;

    	if( isset( $_POST['portfolioLabel'] ) )
        	update_post_meta( $post_id, 'portfolioURL', esc_attr( $_POST['portfolioLabel'] ) );
	}

	function generateShortCode(){
		wp_enqueue_script('isotop-jq');
		wp_enqueue_script('sorting-jq');
		wp_enqueue_style('layout-style');
		$portfolio = get_terms('categoryofwork',array('hide_empty'=>0));
		$items = get_posts(array('post_type'=>'portfolio','post_status'=>'publish','posts_per_page'=>-1));
		
		if(count($portfolio)>0 && count($items)>0){
			$content = '<div class="text-center"><button class=" filterPort" data-filter="*">Show all</button>';
			foreach ($portfolio as $key => $value) {
				$content .= '<button class=" filterPort" data-filter=".'.$value->slug.'">'.$value->name.'</button>';
			}
			$content .= '</div>';

			
			$content .= '<div class="grid container">';
			
			foreach ($items as $key => $value) {
			
				$product_terms = wp_get_object_terms( $value->ID,  'categoryofwork' );
				$portTerms = json_decode(json_encode($product_terms),true);
				$slug = array_column($portTerms, "slug");
				
				$url = (get_post_meta($value->ID,'portfolioURL',true)) ? get_post_meta($value->ID,'portfolioURL',true) : "javascript:void(0)";
				$content .= '<div class="element-item '.implode(" ", $slug).'" data-category="'.implode(",", $slug).'"><div style="" class="image-wrap">
				    <img src="'.wp_get_attachment_url( get_post_thumbnail_id($value->ID) ).'" alt="'.$value->post_title.'" class="img-responsive"></div>
				    <p class="name"><a href="'.$url.'" target="_blank">'.$value->post_title.'</a></p></div>';
				
			}
			
			$content .= '</div>';
		}
		else{
			$content = "<h4>No portfolio</h4>";
		}
		return $content;
	}
}
$portfolio = new MK_Portfolio();