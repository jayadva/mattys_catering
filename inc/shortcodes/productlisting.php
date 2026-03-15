<?php
add_shortcode('productlisting', 'productlisting');

function productlisting ( $atts ) {
    if (isset($atts['catid']) && !empty($atts['catid'])) {
        $taxonomy_name = 'menuproduct_category';
        
        $catids = explode(",", $atts['catid']);
        $display = "";
        foreach ($catids as $catid) {
            $args = array(
                'taxonomy'   => $taxonomy_name,
                'orderby'    => 'ID',
                'order'      => 'ASC',
                'hide_empty' => false,
                'parent'     => $catid,
            );

            $categories = get_terms($args);

            if (!empty($categories) && !is_wp_error($categories)) {
                $display .= '    <div id="productmenucontentcol">';
                $display .= '        <div id="mainproductcontent" class="mainproductcontent">';
                $display .= '        <div id="maincaritems" class="maincaritems">';

                if (isset($atts['title']) && !empty($atts['title'])) {
                    $display .= '           <h2 class="text-green">'.$atts['title'].'</h2>';  
                }

                if (isset($atts['template']) && $atts['template'] == 2 ) {

                    $display .= '<div class="prodaddonprodwrap">';

                    foreach ($categories as $category) {
                        $catid = $category->term_id;

                        $items = array(
                            'posts_per_page' => 50,    
                            'post_type' => 'menuproduct',
                            'orderby'    => 'name',
                            'order'      => 'ASC',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'menuproduct_category',
                                    'field'    => 'term_id',
                                    'terms'    => $catid,
                                ),
                            ),
                        );

                        $the_query = new WP_Query( $items );

                        $display .= '   <div class="mainproductwrap">';
                        $display .= '       <div class="categorytitlewrap">';
                        $display .= '           <div class="productcattitle">';  
                        $display .= '               <h4>'.$category->name.'</h4>';  
                        $display .= '           </div>';  
                        $display .= '           <div class="boxtitlebg"></div>';  
                        $display .= '       </div>';
                        $display .= '       <div class="productinlinelist">';
                        
                        if ( $the_query->have_posts() ) {
                            while ( $the_query->have_posts() ) : $the_query->the_post(); 
                                if (has_post_thumbnail( $the_query->ID ) ) {
                                    $featimage = get_the_post_thumbnail_url($the_query->ID, 'full'); 
                                } else {
                                    $featimage =  esc_url( get_template_directory_uri() ).'/assets/images/noimage.jpg';
                                }
                                
                                $display .= '<div class="productitem">';
                                $display .= '    <div class="productimgwrap">';
                                $display .= '       <div class="productinfo">';
                                $display .= '           <div class="productitemtitle">'.get_the_title().'</div>';
                                if (!empty(get_the_excerpt())) {
                                    $display .= '           <div class="productitemdetail">';
                                    $display .=                 get_the_excerpt();
                                    $display .= '           </div>';
                                }
                                $display .= '       </div>';
                                $display .= '       <button type="button" class="btn btn-default btn-addcart" data-productmenuid="'.get_the_ID().'" data-catid="'.$catid.'" data-catname="'.$category->name.'" data-bs-toggle="modal" data-bs-target="#mattysprodmenumodal">ADD</button>';
                                $display .= '    </div>';
                                $display .= '</div>';

                            endwhile;
                            
                        }
                        $display .= '       </div>'; // productlist end
                        $display .= '   </div>'; // mainproductwrap end
                        
                    }

                    $display .= '</div>'; // prodaddonprodwrap end

                } elseif (isset($atts['template']) && $atts['template'] == 3 ) { 
                    foreach ($categories as $category) {
                        $items = array(
                            'posts_per_page' => 50,    
                            'post_type' => 'menuproduct',
                            'orderby'    => 'name',
                            'order'      => 'ASC',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'menuproduct_category',
                                    'field'    => 'term_id',
                                    'terms'    => $category->term_id,
                                ),
                            ),
                        );

                        $the_query = new WP_Query( $items );

                        $display .= '   <div class="mainproductwrap">';
                        $display .= '       <div class="categorytitlewrap">';
                        $display .= '           <div class="productcattitle">';  
                        $display .= '               <h4>'.$category->name.'</h4>';  
                        $display .= '           </div>';  
                        $display .= '           <div class="boxtitlebg"></div>';  
                        $display .= '       </div>';
                        $display .= '       <div class="productlist moblistitems">';
                        
                        if ( $the_query->have_posts() ) {
                            while ( $the_query->have_posts() ) : $the_query->the_post(); 
                                if (has_post_thumbnail( $the_query->ID ) ) {
                                    $featimage = get_the_post_thumbnail_url($the_query->ID, 'medium'); 
                                } else {
                                    $featimage =  esc_url( get_template_directory_uri() ).'/assets/images/noimage.jpg';
                                }
                                
                                $display .= '<div class="productitemthumbnails">';
                                $display .= '    <div class="productinfo">';
                                $display .= '       <div class="productiteminfo">';
                                $display .= '           <div class="productitemtitle">'.get_the_title().'</div>';
                                $display .= '           <div class="productitemdetail">';
                                $display .=                 get_the_excerpt();
                                $display .= '           </div>';
                                $display .= '       </div>';
                                

                                $setChoosePortionSize = get_field('choose_portion_size');
                                $totalPortionSize = count($setChoosePortionSize);
                                if( isset($setChoosePortionSize) && $totalPortionSize > 1 ) {
                                    $display .= '   <div class="productpricing">';
                                    foreach($setChoosePortionSize as $portionsize) {
                                        if ( strtolower($portionsize['portion_size']) == 'half' && !empty($portionsize['price']) ) {
                                            $display .= '   <div class="productpricingitem">';
                                            $display .= '   H <span>'.$portionsize['price'].'</span>';
                                            $display .= '   </div>';   
                                        } elseif ( strtolower($portionsize['portion_size']) == 'regular' && !empty($portionsize['price']) ) {
                                            $display .= '   <div class="productpricingitem">';
                                            $display .= '   R <span>'.$portionsize['price'].'</span>';
                                            $display .= '   </div>';   
                                        }
                                    }  
                                    $display .= '   </div>';
                                } else {
                                    $display .= '   <div class="productpricing">';
                                    $display .= '       <div class="productpricingitem">';
                                    $display .= '           <span>'.$portionsize['price'].'</span>';
                                    $display .= '       </div>';   
                                    $display .= '   </div>';
                                }
        
                                $display .= '    </div>';
                                $display .= '    <div class="productimgwrap" style="background: url('.esc_url( $featimage ).') center center no-repeat;">';
                                $display .= '       <button type="button" class="btn btn-default btn-addcart" data-productmenuid="'.get_the_ID().'" data-bs-toggle="modal" data-bs-target="#mattysprodmenumodal"></button>';
                                $display .= '    </div>';
                                $display .= '</div>';

                            endwhile;
                            
                        }
                        $display .= '       </div>'; // productlist end
                        $display .= '   </div>'; // mainproductwrap end

                    }              
                } else {
                    foreach ($categories as $category) {
                        $items = array(
                            'posts_per_page' => 50,    
                            'post_type' => 'menuproduct',
                            'orderby'    => 'name',
                            'order'      => 'ASC',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'menuproduct_category',
                                    'field'    => 'term_id',
                                    'terms'    => $category->term_id,
                                ),
                            ),
                        );

                        $the_query = new WP_Query( $items );

                        $display .= '   <div class="mainproductwrap">';
                        $display .= '       <div class="categorytitlewrap">';
                        $display .= '           <div class="productcattitle">';  
                        $display .= '               <h4>'.$category->name.'</h4>';  
                        $display .= '           </div>';  
                        $display .= '           <div class="boxtitlebg"></div>';  
                        $display .= '       </div>';
                        $display .= '       <div class="productlist">';
                        
                        if ( $the_query->have_posts() ) {
                            while ( $the_query->have_posts() ) : $the_query->the_post(); 
                                if (has_post_thumbnail( $the_query->ID ) ) {
                                    $featimage = get_the_post_thumbnail_url($the_query->ID, 'full'); 
                                } else {
                                    $featimage =  esc_url( get_template_directory_uri() ).'/assets/images/noimage.jpg';
                                }
                                
                                $display .= '<div class="productitem">';
                                $display .= '    <div class="productimgwrap">';
                                $display .= '       <img src="'.esc_url( $featimage ).'" alt="">';
                                $display .= '       <button type="button" class="btn btn-default btn-addcart" data-productmenuid="'.get_the_ID().'" data-bs-toggle="modal" data-bs-target="#mattysprodmenumodal">ADD</button>';
                                $display .= '    </div>';
                                $display .= '    <div class="productinfo">';
                                $display .= '        <div class="productitemtitle">'.get_the_title().'</div>';
                                $display .= '        <div class="productitemdetail">';
                                $display .=             get_the_excerpt();
                                $display .= '        </div>';
                                $display .= '    </div>';
                                $display .= '</div>';

                            endwhile;
                            
                        }
                        $display .= '       </div>'; // productlist end
                        $display .= '   </div>'; // mainproductwrap end

                    }
                }

                $display .= '       </div>'; // maincaritems end
                $display .= '   </div>'; // mainproductcontent end
                $display .= '</div>';
                
                wp_reset_postdata();
                
            } else {
                echo 'No categories found for this custom post type.';
            }
        }

        return $display;

    }
}

add_action('wp_ajax_productmenumodal', 'productmenumodal');
add_action('wp_ajax_nopriv_productmenumodal', 'productmenumodal');

function productmenumodal () {
    $productId = $_POST['productId'];
    $catId = $_POST['catid'] ? $_POST['catid'] : '';
    $catName = $_POST['catname'] ? $_POST['catname'] : '';
    
    $productitems = array(
        'p' => $productId,
        'posts_per_page' => 50,    
        'post_type' => 'menuproduct',
        'orderby'    => 'name',
        'order'      => 'ASC',
    );

    $prodquery = new WP_Query( $productitems );

    if ( $prodquery->have_posts() ) {
        $display = '';
        while ( $prodquery->have_posts() ) : $prodquery->the_post(); 
            if (has_post_thumbnail( $prodquery->ID ) ) {
                $featimage = get_the_post_thumbnail_url($prodquery->ID, 'full'); 
                $parent_id = get_post_field('post_parent', $prodquery->ID);

                $display .= '<div class="modal-header">';
                $display .= '   <div class="modal-header-img" id="productimgsrc" data-productimg="'.esc_url( $featimage ).'" style="background-image: url('.$featimage.');"></div>';  
                
                $popular = get_field('popular');
                if(isset($popular) && !empty($popular)) {
                    $display .= '<img src="'.get_template_directory_uri().'/assets/images/popular.png" alt="" class="popularimg">';
                } 

                $display .= '</div>';
            } 
            

            $setAddOnDrinks = get_field('add_on_drinks');
            if($setAddOnDrinks) {
                $drinkscls = "itemdrinks";
            } else {
                $drinkscls = "";
            }

            // INITIALIZE Portion Size
            $choose_portion_size = get_field('choose_portion_size');
            $totale_portion_size = count($choose_portion_size);

            $display .= '<form action="" id="productcartform" class="productcartform" method="post" onsubmit="return false;">';
            $display .= '   <div class="productmodalcontent '.$drinkscls.'">';
            $display .= '       <div class="prodmodalinfowrap">';
            $display .= '           <div class="productmodaltitle d-flex flex-row justify-content-between align-items-center">';
            $display .= '               <h4 class="text-green" id="producttitle" data-producttitle="'.esc_attr(get_the_title()).'">'.get_the_title().'</h4>';
            $display .= '           </div>';
            $display .= '           <div class="productmodaldesc">';
            $display .= '               <p>'.get_the_excerpt().'</p>';
            $display .= '           </div>';
            $display .= '       </div>';
            $display .= '       <div class="prodmodalpricewrap">';

            if( isset($choose_portion_size) && $totale_portion_size > 1 ) {
                foreach($choose_portion_size as $portionpricesize) {
                    if ($portionpricesize['price']) {
                        $firstLetter = substr($portionpricesize['portion_size'], 0, 1);
                        $display .= '   <div class="modalprodpriceitem">';
                        $display .= '       <span>'.$firstLetter.'</span> '.$portionpricesize['price'];
                        $display .= '   </div>';   
                    }        
                }
            } else {
                foreach($choose_portion_size as $portionpricesize) {
                    if (isset($portionpricesize['price']) ) {
                        $display .= '   <div class="modalproductpricingitem">';
                        $display .=         $portionpricesize['price'];
                        $display .= '   </div>';
                    }
                }
            } 
                     
            $display .= '       </div>';
            $display .= '   </div>';


            // Choose portion size
            if( $totale_portion_size > 1 ) {
                if($choose_portion_size) {
                    $display .= '<div class="productoptions">';
                    $display .= '	<label for="portion-size">Choose portion size</label>';
                    $display .= '   <div class="selectwrapper">';
                    $display .= '   <select name="portion_size" id="portion_size">';
                    $display .= '   <option value="">Select</option>';
                    foreach($choose_portion_size as $portion) {
                        if ($portion['price']) {
                            $portionPrice = ' ('.$portion['price'].')';
                        }
                        $display .= '   <option value="'.$portion['portion_size'].'" data-price="'.$portion['price'].'">'.$portion['portion_size'].'</option>';
                    }
                    $display .= '   </select>';   
                    $display .= '   </div>';    
                    $display .= '</div>';               
                }
            } else {
                foreach($choose_portion_size as $portion) {
                    if ($portion['price']) {
                        $portionPrice = $portion['price'];
                    }
                    $display .= '   <input type="hidden" name="portion_size" id="portion_size" value="" data-price="'.$portionPrice.'">';   
                }                
            }          
               
            // Choose portion size
            $choose_cut_preferences = get_field('choose_cut_preference');
            if($choose_cut_preferences) {
                $display .= '<div class="productoptions">';
                $display .= '	<label for="cut_preference">Choose Cut Preference</label>';
                $display .= '   <div class="selectwrapper">';
                $display .= '   <select name="cut_preference" id="cut_preference">';
                $display .= '   <option value="">Select</option>';
                foreach($choose_cut_preferences as $choosecutpreference) {
                    $display .= '   <option value="'.$choosecutpreference['cut_preference'].'">'.$choosecutpreference['cut_preference'].'</option>';
                }
                $display .= '   </select>';
                $display .= '   </div>';
                $display .= '</div>';
            }

			$display .= '<div class="productoptions">';
			$display .= '	<label for="quantityInput">Quantity</label>';
			$display .= '	<div class="input-group quantity-container" style="width: 100px;">';
			$display .= '		<button class="btn btnquantity decrement-btn" type="button">&minus;</button>';
			$display .= '		<input type="text" class="form-control text-center qty-input prodquantity" name="prodquantity" id="prodquantity" value="1" min="1" max="100">';
			$display .= '		<button class="btn btnquantity increment-btn" type="button">&plus;</button>';
			$display .= '	</div>';
			$display .= '</div>';


            $modno_sandwiches = get_field('modify_no_sandwich');
            $modno_sandwiches_list = get_field('modify_no_sandwich_list');

            if ($modno_sandwiches || $modno_sandwiches_list) {
                $display .= '<div class="productoptions">';
                $display .= '   <div id="modifysandwichwrap" class="modifysandwichwrap">';
                $display .= '	    <label for="modify-your-sandwich">Modify your sandwich</label>';
                $display .= '	    <button type="button" class="btn btn-modifysandwich"><img src="'.get_template_directory_uri().'/assets/images/chevron-bottom.svg"></button>';
                $display .= '   </div>';
                $display .= '</div>';
            }
            
            $display .= '<div id="productoptionaddons" class="productoptionaddons">';
            $display .= '   <div class="productoptionaddonsitems">'; // BOF productoptionaddonsitems 1

            if($modno_sandwiches) {
                foreach($modno_sandwiches as $modNoSandwich) {
                    $display .= '<div class="checkboxwrap">';
					$display .=	'<label class="checkboxlabel">'.$modNoSandwich;
					$display .=	'		<input type="checkbox" name="modify_no_sandwich[]" value="'.esc_attr($modNoSandwich).'">';
					$display .=	'		<span class="checkmark"></span>';
					$display .=	'	</label>';
					$display .=	'</div>';
                }
            }

            if($modno_sandwiches_list) {
                foreach($modno_sandwiches_list as $modNoSandwichList) {
                    if(isset($modNoSandwichList['no_sandwich_list_item']) && !empty($modNoSandwichList['no_sandwich_list_item'])) {
                        
                        $sandwich_item = $modNoSandwichList['no_sandwich_list_item'];

                        $display .= '<div class="checkboxwrap">';
                        $display .=	'<label class="checkboxlabel">'.$sandwich_item;
                        $display .=	'		<input type="checkbox" name="modify_no_sandwich[]" value="'.esc_attr($sandwich_item).'">';
                        $display .=	'		<span class="checkmark"></span>';
                        $display .=	'	</label>';
                        $display .=	'</div>';
                    }
                }
            }  

            $display .= '   </div>'; //EOF productoptionaddonsitems 1
            $display .= '   <div class="productoptionaddonsitems">'; // BOF productoptionaddonsitems 2

            $modAddon_sandwiches = get_field('modify_addon_sandwich');
            if($modAddon_sandwiches) {
                foreach($modAddon_sandwiches as $modAddonSandwich) {
                    $display .= '<div class="checkboxwrap">';
                    $display .=	'<label class="checkboxlabel">'.$modAddonSandwich;
                    $display .=	'		<input type="checkbox" name="modify_addon_sandwich[]" value="'.esc_attr($modAddonSandwich).'">';
                    $display .=	'		<span class="checkmark"></span>';
                    $display .=	'	</label>';
                    $display .=	'</div>';
                }
            }

            $modAddon_sandwiches_list = get_field('modify_addon_sandwich_list');
            if($modAddon_sandwiches_list) {
                foreach($modAddon_sandwiches_list as $modAddonSandwichesList) {
                    if(isset($modAddonSandwichesList['addon_sandwich_list_item']) && !empty($modAddonSandwichesList['addon_sandwich_list_item'])) {

                        $sandwich_item_addon = $modAddonSandwichesList['addon_sandwich_list_item'];
                        $sandwich_addonamount = $modAddonSandwichesList['add_on_amount'];

                        if($sandwich_addonamount) {
                            $addonamountvalue = ' ('.$sandwich_addonamount.')';
                        }else {
                            $addonamountvalue = ""; 
                        }
                        
                        $display .= '<div class="checkboxwrap">';
                        $display .=	'<label class="checkboxlabel">';
                        $display .=	'   <div class="addonamountwrap">';
                        $display .=	'       <div class="addonlabel">'.$sandwich_item_addon.'</div>';
                        $display .=	'       <div class="addonamount">'.$sandwich_addonamount.'</div>';
                        $display .=	'   </div>';
                        $display .=	'	<input type="checkbox" name="modify_addon_sandwich[]" value="'.esc_attr($sandwich_item_addon).''.$addonamountvalue.'" data-addonamount="'.esc_attr(trim(str_replace("+", "", $sandwich_addonamount))).'">';
                        $display .=	'	<span class="checkmark"></span>';
                        $display .=	'	</label>';
                        $display .=	'</div>';
                    }
                }
            } 

            $setAddOnDrinks = get_field('add_on_drinks');
            if($setAddOnDrinks) {
                $addOnDrinks = 1;
            } else {
                $addOnDrinks = 0;
            }
            
            $display .= '   </div>'; //EOF productoptionaddonsitems 2
            $display .= '</div>'; // EOF productoptionaddons
            $display .= '<input type="hidden" name="productid" id="productid" value="'.$productId.'">';
            $display .= '<input type="hidden" name="addondrinks" id="addondrinks" value="'.$addOnDrinks.'">';
            $display .= '<input type="hidden" name="catid" id="catid" value="'.$catId .'">';
            $display .= '<input type="hidden" name="catname" id="catname" value="'.$catName .'">';
            $needsValidation = ($totale_portion_size > 1) || !empty($choose_cut_preferences);
            $btnLockedClass = $needsValidation ? ' btn-additem-locked' : '';
            $display .= '<div class="modal-footer">';
			$display .= '   <button type="button" class="btn btn-primary btn-additem'.$btnLockedClass.'">ADD ITEM</button>';
			$display .= '</div>';

            $display .= '</form>';

        endwhile;

        $response['success'] = 1;
        $response['data'] = $display;

    } else {
        $response['success'] = 0;
        $response['data'] = 'No products found';
    } 

    wp_reset_postdata();

    wp_send_json( $response, $status_code, $options );
    wp_die();
}


add_shortcode('productcart', 'productcart');

function productcart () {
    $display = '       <aside id="sidebar" class="sidebar">';
    $display .= '           <button type="button" class="btn btn-default btn-closesidebar">';
    $display .= '               <img src="'.get_template_directory_uri().'/assets/images/closeicon.svg" alt="">';
    $display .= '           </button>';
    $display .= '           <div id="sidebarwrapper" class="sidebarwrapper">';
    $display .= '               <div id="sidebarcontent" class="sidebarcontent">';
    $display .= '                   <div class="carttitledescwrap">';
    $display .= '                       <h4 class="text-green">YOUR ORDER</h4>';
    $display .= '                       <div id="cartdesc" class="cartdesc">A minimum catering order of $200 applies to all Matty\'s Sandwiches catering bookings.</div>';
    $display .= '                   </div>';
    $display .= '                   <div id="sidebarcartitemlist" class="sidebarcartitemlist sandwichitems"></div>';
    $display .= '                   <div id="maindrinkswrap" class="maindrinkswrap">';
    $display .= '                       <h4 id="addondrinkstitle" class="addondrinkstitle"></h4>';     
    $display .= '                       <div id="sidebarcartitemlistdrinks" class="sidebarcartitemlist"></div>';    
    $display .= '                   </div>';  
    $display .= '                   <div id="carttotalwrap" class="carttotalwrap">';
    $display .= '                       <div class="carttotaltext">TOTAL</div>';
    $display .= '                       <div class="carttotalprice">$0.00</div>';
    $display .= '                   </div>';            
    $display .= '                   <div class="confirmorderwrap">';
    $display .= '                       <a href="'.get_permalink(150).'" id="btn-confirmorder" class="btn btn-primary btn-confirmorder" disabled>Continue enquiry</a>';
    $display .= '                   </div>';
    $display .= '               </div>';
    $display .= '               <div id="mainuberwrap" class="mainuberwrap">';
    $display .= '                   <div class="uborcontentwrap">';
    $display .= '                       <div class="ubercontent">Can\'t reach the minimum? You can still order Matty\'s Sandwiches through UberEats for smaller orders.</div>';
    $display .= '                       <img src="'.get_template_directory_uri().'/assets/images/uber.png" alt="Uber">';
    $display .= '                   </div>';
    $display .= '                   <a class="btn btn-uber" href="https://www.ubereats.com/au/store/mattys-sandwiches-parramatta/cFvE-_rGT_CMWVEcejPA6g?srsltid=AfmBOoo2YOJZXoR6tDxGQIsjx1HAK3pYsPfXZDGtv-zjCVcysxBmBIfs" target="_blank">ORDER ON UBEREATS</a>';
    $display .= '               </div>';   
    $display .= '           </div>';        
    $display .= '       </aside>';   
    $display .= '       <div id="sidebartrigger" class="sidebartrigger"></div>';

    return $display;
}