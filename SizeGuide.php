<?php
// This function tirgger sizeguide weather using what shortcode
public function triggerSizeGuide($atts) {

		global $product;

		$hide   = get_option( 'wc_size_guide_hide' );
		$output = '';

		$atts = shortcode_atts(
			array(
				'postid' => '',
                'button' =>    '',
                'button_value' =>    '',
			), $atts );
        $btn_true = $atts['button'];
        $btn_value = $atts['button_value'];
        if(empty($btn_value)){
            $btn_val = $this->getSgOption( "wc_size_guide_button_label", esc_html__( "Size Guide", "ct-sgp" ) ) . ' '.get_the_title($atts['postid']).'';
        }else{
            $btn_val = $btn_value;
        }

		if ( $product ) {
			if (!$this->hasSizeGuide($product->get_id())){
				return $output;
			}

			$productStock        = $product->get_stock_quantity();
			$productAvailability = $product->get_availability();

			if ( $hide != 'yes' || $productAvailability['class'] != 'out-of-stock' ) {
				$trigger = $this->getSgOption( 'wc_size_guide_button_style', 'ct-trigger-button' );

				$align = $this->getSgOption( 'wc_size_guide_button_align', 'left' );
				if ( $this->getSgOption( 'wc_size_guide_button_position' ) == 'ct-position-add-to-cart' ) {
					$align = '';
				}
				$clear = $this->getSgOption( 'wc_size_guide_button_clear', 'no' );

				$mleft   = $this->getSgOption( 'wc_size_guide_button_margin_left', 0 );
				$mtop    = $this->getSgOption( 'wc_size_guide_button_margin_top', 0 );
				$mright  = $this->getSgOption( 'wc_size_guide_button_margin_right', 0 );
				$mbottom = $this->getSgOption( 'wc_size_guide_button_margin_bottom', 0 );

				$margins = '';

				if ( $mleft != 0 ) {
					$margins .= 'margin-left: ' . (int) $mleft . 'px; ';
				}
				if ( $mtop != 0 ) {
					$margins .= 'margin-top: ' . (int) $mtop . 'px; ';
				}
				if ( $mright != 0 ) {
					$margins .= 'margin-right: ' . (int) $mright . 'px; ';
				}
				if ( $mbottom != 0 ) {
					$margins .= 'margin-bottom: ' . (int) $mbottom . 'px; ';
				}

				if ( $trigger == 'ct-trigger-button' ) {

					$sg_set_icon = $this->getSgOption( 'wc_size_guide_button_icon' );

					$sg_icon_markup = ( $sg_set_icon == 'null' ) || $sg_set_icon == 'fa fa-blank' ? '' : '<span class="' . $sg_set_icon . '"></span>';
					$output = '<a class="open-popup-link ' . $this->getSgOption( 'wc_size_guide_button_class', 'button_sg' ) . '" href="#ct_size_guide-'.$this->sg_id.'" style="float: ' . $align . '; ' . $margins . '">' . $sg_icon_markup . $this->getSgOption( "wc_size_guide_button_label", esc_html__( "Size Guide", "ct-sgp" ) ) . '</a>';
				} else {
					$output = '<a class="open-popup-link" href="#ct_size_guide-'.$this->sg_id.'" style="float: ' . $align . '; ' . $margins . '">' . $this->getSgOption( "wc_size_guide_button_label", esc_html__( "Size Guide", "ct-sgp" ) ) . '</a>';
				}
				if ( $clear == 'no' ) {
					$output .= '<div class="clearfix"></div>';
				}
			}

		}else{
           //NOTE: This will display on the pages only and not on the product
            $sg_post_id  = $atts['postid'];
            ob_start();
            if($btn_true == 'true'){
                // display if button = true
               echo '<a class="open-popup-link '. $this->getSgOption( 'wc_size_guide_button_class', 'button_sg' ).'" href="#ct_size_guide-'.$sg_post_id.'" ">' .$btn_val.'</a>';
                $this->sg_id = $sg_post_id;
                $size_table  = get_post_meta( $sg_post_id, '_ct_sizeguide' );
                if ( $size_table ) {
                    $size_table   = $size_table[0];
                    $this->tables = $size_table;
                    $output =  $this->renderSizeGuideTableOutput( $size_table, $sg_post_id, false, false );
                }
            }else{
                // display if button = empty/null
                $this->sg_id = $sg_post_id;
                $size_table  = get_post_meta( $sg_post_id, '_ct_sizeguide' );
                if ( $size_table ) {
                    $size_table   = $size_table[0];
                    $this->tables = $size_table;
                    $output =  $this->renderSizeGuideTableOutput( $size_table, $sg_post_id, false, true );
                }
            }



			$output = ob_get_clean();
		}

		return $output;
	}