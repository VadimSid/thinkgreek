<?php 
/*
* Template Name: Search Car Listings
*/
get_header(); 
        
?>
<div id="inner-wrap" class="slide ">

    <div id="body-container" class="container">

        <?php 

        /**
         * Shandora Before Loop Hook
         *
         * @hooked shandora_get_page_header - 1
         * @hooked shandora_search_get_listing - 2
         * @hooked shandora_open_main_content_row - 5
         * @hooked shandora_get_left_sidebar - 10
         * @hooked shandora_open_main_content_column - 15
         *
         */

        do_atomic('before_loop'); ?>


                <?php 
                        
                    $prefix = bon_get_prefix();    
                    // taxonomy query and meta query arrays
                    $tax_query = array();
                    $meta_query = array();
                    
                    // if property-type is set add it to taxonomy query
                    if(isset($_GET['body_type']) && !empty($_GET['body_type']) )
                    {
                        $body_type = $_GET['body_type'];
                        if( $body_type != 'any' )
                        {                               
                            $tax_query[] = array(
                                                'taxonomy' => 'body-type',
                                                'field' => 'slug',
                                                'terms' => $body_type
                                            );
                        }
                    }


                    
                    // if city is set add it to taxonomy query
                    if(isset($_GET['dealer_location']) && !empty($_GET['dealer_location']))
                    {
                        $location = $_GET['dealer_location'];  
                        if( $location != 'any' )
                        {                           
                            $tax_query[] = array(
                                            'taxonomy' => 'dealer-location',
                                            'field' => 'slug',
                                            'terms' => $location
                                        );
                        }
                    }

                    // if city is set add it to taxonomy query
                    if(isset($_GET['dealer_location_level1']) && !empty($_GET['dealer_location_level1']))
                    {

                        if(isset($_GET['dealer_location_level2']) && !empty($_GET['dealer_location_level2']) && $_GET['dealer_location_level2'] != 'any') {

                            if(isset($_GET['dealer_location_level3']) && !empty($_GET['dealer_location_level3']) && $_GET['dealer_location_level3'] != 'any') {
                                  $tax_query[] = array(
                                    'taxonomy' => 'dealer-location',
                                    'field' => 'slug',
                                    'terms' => $_GET['dealer_location_level3']
                                );
                                
                            } else {
                       
                                $tax_query[] = array(
                                    'taxonomy' => 'dealer-location',
                                    'field' => 'slug',
                                    'terms' => $_GET['dealer_location_level2']
                                );
                                
                            }

                        } else {
                            $location = $_GET['dealer_location_level1'];  
                            if( $location != 'any' )
                            {                           
                                $tax_query[] = array(
                                                'taxonomy' => 'dealer-location',
                                                'field' => 'slug',
                                                'terms' => $location
                                            );
                            }
                        }
                       
                    }

                    // if city is set add it to taxonomy query
                    if(isset($_GET['manufacturer']) && !empty($_GET['manufacturer']))
                    {
                        $manufacturer = $_GET['manufacturer'];  
                        if( $manufacturer != 'any' )
                        {                           
                            $tax_query[] = array(
                                            'taxonomy' => 'manufacturer',
                                            'field' => 'slug',
                                            'terms' => $manufacturer
                                        );
                        }
                    }

                    // if city is set add it to taxonomy query
                    if(isset($_GET['manufacturer_level1']) && !empty($_GET['manufacturer_level1']))
                    {

                        if(isset($_GET['manufacturer_level2']) && !empty($_GET['manufacturer_level2']) && $_GET['manufacturer_level2'] != 'any') {

                            if(isset($_GET['manufacturer_level3']) && !empty($_GET['manufacturer_level3']) && $_GET['manufacturer_level3'] != 'any') {
                                  $tax_query[] = array(
                                    'taxonomy' => 'manufacturer',
                                    'field' => 'slug',
                                    'terms' => $_GET['manufacturer_level3']
                                );
                                
                            } else {
                       
                                $tax_query[] = array(
                                    'taxonomy' => 'manufacturer',
                                    'field' => 'slug',
                                    'terms' => $_GET['manufacturer_level2']
                                );
                                
                            }

                        } else {
                            $man = $_GET['manufacturer_level1'];  
                            if( $man != 'any' )
                            {                           
                                $tax_query[] = array(
                                                'taxonomy' => 'manufacturer',
                                                'field' => 'slug',
                                                'terms' => $man
                                            );
                            }
                        }
                       
                    }

                 

                    if(isset($_GET['car_feature']) && !empty($_GET['car_feature']))
                    {
                        $feature = $_GET['car_feature'];  
                        if( $feature != 'any' )
                        {                           
                            $tax_query[] = array(
                                            'taxonomy' => 'car-feature',
                                            'field' => 'slug',
                                            'terms' => $feature
                                        );
                        }
                    }
                    
                    // if beds are set add it to meta query
                    if(isset($_GET['ancap']) && !empty($_GET['ancap']) )
                    {
                        $ancap = $_GET['ancap'];
                        if( $ancap != 'any' )
                        {                               
                            $meta_query[] = array(
                                'key' => bon_get_prefix() . 'listing_ancap',
                                'value' => $ancap,
                                'compare' => '=',
                                'type'=> 'NUMERIC'
                            );
                        }
                    }

            
                    if(isset($_GET['car_status']) && !empty($_GET['car_status']) )
                    {
                        $status = $_GET['car_status'];
                        if( $status != 'any' )
                        {                               
                            $meta_query[] = array(
                                'key' => bon_get_prefix() . 'listing_status',
                                'value' => $status,
                                'compare' => '=',
                            );
                        }
                    }



                    if(isset($_GET['transmission']) && !empty($_GET['transmission']) )
                    {
                        $transmission = $_GET['transmission'];
                        if( $transmission != 'any' )
                        {                               
                            $meta_query[] = array(
                                'key' => bon_get_prefix() . 'listing_transmission',
                                'value' => $transmission,
                                'compare' => '=',
                            );
                        }
                    }

                    if(isset($_GET['fuel_type']) && !empty($_GET['fuel_type']) )
                    {
                        $fuel_type = $_GET['fuel_type'];
                        if( $fuel_type != '' )
                        {                               
                            $meta_query[] = array(
                                'key' => bon_get_prefix() . 'listing_fueltype',
                                'value' => $fuel_type,
                                'compare' => 'LIKE',
                            );
                        }
                    }

                    if(isset($_GET['yearbuilt']) && !empty($_GET['yearbuilt']) )
                    {
                        $yearbuilt = $_GET['yearbuilt'];
                        if( $yearbuilt != '' )
                        {                               
                            $meta_query[] = array(
                                'key' => bon_get_prefix() . 'listing_yearbuild',
                                'value' => $yearbuilt,
                                'compare' => '=',
                            );
                        }
                    }

                    if(isset($_GET['min_yearbuilt']) && isset($_GET['max_yearbuilt']) )
                    {   
                        $min_year = intval( $_GET['min_yearbuilt'] );
                        $max_year = intval( $_GET['max_yearbuilt'] );

                        if( empty( $max_year ) && !empty( $min_year ) ) { 
                            $meta_query[] = array( 
                                'key' => bon_get_prefix() . 'listing_yearbuild', 
                                'value' => $min, 
                                'type' => 'NUMERIC', 
                                'compare' => '>='
                            ); 
                        } else if( empty( $min_year ) && !empty( $max_year ) ) { 
                            $meta_query[] = array( 
                                'key' => bon_get_prefix() . 'listing_yearbuild', 
                                'value' => $max_year, 
                                'type' => 'NUMERIC', 
                                'compare' => '<='
                            ); 
                        } else if( !empty( $min_year ) && !empty( $max_year) ) { 
                            
                            if( $min_year == $max_year ) {
                                $meta_query[] = array( 
                                    'key' => bon_get_prefix() . 'listing_yearbuild', 
                                    'value' => $min_year, 
                                    'type' => 'NUMERIC', 
                                    'compare' => '='
                                ); 
                            } else {
                                $meta_query[] = array( 
                                    'key' => bon_get_prefix() . 'listing_yearbuild', 
                                    'value' => array( $min_year, $max_year ), 
                                    'type' => 'NUMERIC', 
                                    'compare' => 'BETWEEN'
                                ); 
                            }
                        } 
                    }

                    if(isset($_GET['exterior_color']) && !empty($_GET['exterior_color']) )
                    {
                        $exterior_color = $_GET['exterior_color'];
                        if( $exterior_color != '' )
                        {                               
                            $meta_query[] = array(
                                'key' => bon_get_prefix() . 'listing_extcolor',
                                'value' => $exterior_color,
                                'compare' => 'LIKE',
                            );
                        }
                    }

                    if(isset($_GET['interior_color']) && !empty($_GET['interior_color']) )
                    {
                        $interior_color = $_GET['interior_color'];
                        if( $interior_color != '' )
                        {                               
                            $meta_query[] = array(
                                'key' => bon_get_prefix() . 'listing_intcolor',
                                'value' => $interior_color,
                                'compare' => 'LIKE',
                            );
                        }
                    }

                    if(isset($_GET['reg_number']) && !empty($_GET['reg_number']) )
                    {
                        $reg_number = $_GET['reg_number'];
                        if( $reg_number != 'any' )
                        {                               
                            $meta_query[] = array(
                                'key' => bon_get_prefix() . 'listing_reg',
                                'value' => $reg_number,
                                'compare' => '=',
                            );
                        }
                    }

                    // if both of the min and max prices are specified then add them to meta query
                    if(isset($_GET['min_price']) && isset($_GET['max_price']) )
                    {

                        $min_price = intval($_GET['min_price']);
                        $max_price = intval($_GET['max_price']);
                        $the_max = intval(bon_get_option('price_range_max'));
                        //ignore max price
                        if( $min_price >= 0 && $max_price == $the_max ) {
                            $meta_query[] = array(
                                    'key' => bon_get_prefix() . 'listing_price',
                                    'value' => $min_price,
                                    'type' => 'NUMERIC',
                                    'compare' => '>='
                                );
                        }
                        
                        else if( $min_price >= 0 && $max_price > $min_price )
                        {                               
                            $meta_query[] = array(
                                    'key' => bon_get_prefix() . 'listing_price',
                                    'value' => array( $min_price, $max_price ),
                                    'type' => 'NUMERIC',
                                    'compare' => 'BETWEEN'
                                );
                        } 
/*
                        if( empty( $max_price ) && !empty( $min_price ) ) {
                            $meta_query[] = array(
                                'key' => bon_get_prefix() . 'listing_price',
                                'value' => $min_price,
                                'type' => 'NUMERIC',
                                'compare' => '>='
                            );
                        } else if( empty( $min_price ) && !empty( $max_price ) ) {
                            $meta_query[] = array(
                                'key' => bon_get_prefix() . 'listing_price',
                                'value' => $min_price,
                                'type' => 'NUMERIC',
                                'compare' => '<='
                            );
                        } else {
                            $meta_query[] = array(
                                'key' => bon_get_prefix() . 'listing_price',
                                'value' => array( $min_price, $max_price ),
                                'type' => 'NUMERIC',
                                'compare' => 'BETWEEN'
                            );
                        }*/
                    }

                     // if both of the min and max prices are specified then add them to meta query
                    if(isset($_GET['min_mileage']) && isset($_GET['max_mileage']) )
                    {

                        $min = intval($_GET['min_mileage']);
                        $max = intval($_GET['max_mileage']);

                        $the_max = intval(bon_get_option('maximum_mileage'));

                        //ignore max 
                        if( $min >= 0 && $max == $the_max ) {
                            $meta_query[] = array(
                                    'key' => bon_get_prefix() . 'listing_mileage',
                                    'value' => $min,
                                    'type' => 'NUMERIC',
                                    'compare' => '>='
                                );
                        }

                        else if( $min >= 0 && $max > $min )
                        {                               
                            $meta_query[] = array(
                                                'key' => bon_get_prefix() . 'listing_mileage',
                                                'value' => array( $min, $max ),
                                                'type' => 'NUMERIC',
                                                'compare' => 'BETWEEN'
                                            );
                        }


                    }
               
                    
                    // if two taxonomies exist then specify the relation
                    $tax_count = count($tax_query);
                    if($tax_count > 1)
                    {               
                        $tax_query['relation'] = 'AND';
                    }
                    
                    $meta_count = count($meta_query);
                    if($meta_count > 1)
                    {               
                        $meta_query['relation'] = 'AND';
                    }
                    
                    $numberposts = (bon_get_option('listing_per_page')) ? bon_get_option('listing_per_page') : 8;
                    
                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                    $search_args = array(
                                'post_type' => 'car-listing',
                                'posts_per_page' => $numberposts,
                                'paged' => $paged,
                                'post_title' => isset( $_GET['title'] ) ? $_GET['title'] : '',                          
                    );
                    
                    if($tax_count > 0)
                    {
                        $search_args['tax_query'] = $tax_query;
                    }
                    
                    if($meta_count > 0)
                    {
                        $search_args['meta_query'] = $meta_query;
                    }

                    $orderby = bon_get_option('listing_orderby');
                    $order = bon_get_option('listing_order', 'DESC');
                    $key = '';

                    if(isset($_GET['search_orderby'])) {
                        $orderby = $_GET['search_orderby'];
                    }
                    
                    if(isset($_GET['search_order'])) {
                        $order = $_GET['search_order'];
                    }

                    switch ( $orderby ) {
                        case 'price':
                            $orderby = 'meta_value_num';
                            $key = bon_get_prefix() . 'listing_price';
                            break;
                        
                        case 'title':
                            $orderby = 'title';

                            break;

                        case 'size':
                            $orderby = 'meta_value_num';
                            $key = bon_get_prefix() . 'listing_mileage';

                            break;

                        default:
                            $orderby = 'date';
                            break;
                    }
                    
                   

                    $search_args['meta_key'] = $key;
                    $search_args['orderby'] = $orderby;
                    $search_args['order'] = $order;
                    // wp query
                    $wp_query = new WP_Query( $search_args );

                    ?>

                <?php bon_get_template_part('loop', 'car-listing'); ?>

                <?php bon_get_template_part( 'loop','nav' ); // Loads the loop-nav.php template. ?>
            <?php 

            /**
             * Shandora After Loop Hook
             *
             * @hooked shandora_close_main_content_column - 1
             * @hooked shandora_get_right_sidebar - 5
             * @hooked shandora_close_main_content_row - 10
             *
             */

            do_atomic('after_loop'); ?>

    </div>


<?php get_footer(); ?>
