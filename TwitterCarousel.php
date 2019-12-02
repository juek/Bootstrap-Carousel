<?php
defined('is_running') or die('Not an entry point...');


class TwitterCarousel{

	const content_key = 'Carousel232';

	/**
	 * Determine if the $type matches this content key
	 */
	static function ContentKeyMatch($type){
		global $addonFolderName;
		if( $type === self::content_key ){
			return true;
		}
	}


	/**
	 * @static
	 */
	static function GetHead() {
		global $page, $addonRelativeCode;
		$page->head_js[]  = $addonRelativeCode . '/jquery.mobile.custom.js';
		$page->head_js[]  = $addonRelativeCode . '/carousel.js';
		if ( $section_data['alt_style'] ){
			$page->css_user[] = $addonRelativeCode . '/carousel2.css';}
		else{
			$page->css_user[] = $addonRelativeCode . '/carousel.css';
		}
	}


	static function SectionTypes($section_types, $generated_key = false ){
		$section_types[self::content_key] = array();
		$section_types[self::content_key]['label'] = 'Bootstrap Carousel Gallery';
		return $section_types;
	}


	static function NewSections($links){
		global $addonRelativeCode;
		foreach($links as $key => $section_type_arr){
			if( $section_type_arr[0] == self::content_key ){
				$links[$key] = array(self::content_key, $addonRelativeCode . '/icon/section.png');
			}
		}
		return $links;
	}


	static function SectionToContent($section_data){
		if( !self::ContentKeyMatch($section_data['type']) ){
			return $section_data;
		}
		global $dataDir;
		//update content
		if( !isset($section_data['content_version']) ){
			$section_data['content'] = self::GenerateContent($section_data);
		}
		self::AddComponents();
		return $section_data;
	}


	static function GenerateContent($section_data){
		global $dataDir;
		$section_data += array('images'=>array(),'height'=>'400');

		$id = 'carousel_'.time();

		$images = '';
		$indicators = '';
		$j = 0;
		foreach($section_data['images'] as $i => $img){
			if( empty($img) ){
				continue;
			}
			$caption = trim($section_data['captions'][$i]);

			$class = '';
			if( $j == 0 ){
				$class = 'active';
			}


			//images
			$caption_class = '';
			if( empty($caption) ){
				$caption_class = 'no_caption';
			}
			$images .= '<div class="item carousel-item '.$class.'">'
						.'<img src="'.common::GetDir('/include/imgs/blank.gif').'" style="background-image:url('.$img.')" alt="'.$caption.'">'
						.'<div class="caption carousel-caption '.$caption_class.'">'.$caption.'</div>'
						.'</div>';

			//indicators
			$thumb_path = common::ThumbnailPath($img);
			$indicators .= '<li data-target="#'.$id.'" data-slide-to="'.$j.'" class="'.$class.'">'
							.'<a href="'.$img.'" aria-hidden="true">'
							.'<img src="'.$thumb_path.'" alt="" class="d-none" aria-hidden="true">'
							.'</a>'
							.'</li>';
			$j++;
		}

		ob_start();

		$class = 'gp_twitter_carousel carousel slide';
		if( !$section_data['auto_start'] ){
			$class .= ' start_paused';
		}
		$attr = ' data-speed="5000"';
		if( isset($section_data['interval_speed']) && is_numeric($section_data['interval_speed']) ){
			$attr = ' data-speed="'.$section_data['interval_speed'].'"';
		}
		$attr .= ' data-pause="hover"';
		
		if ($section_data['height'] == '') {
			$section_data['height'] = 'auto';
		}
		echo '<div id="'.$id.'" class="'.$class.'"'.$attr.'>';
		echo '<div style="padding-bottom:'.$section_data['height'].'">';
		// Indicators
		echo '<ol class="carousel-indicators">';
		echo $indicators;
		echo '</ol>';

		// Carousel items
		echo '<div class="carousel-inner">';
		echo $images;
		echo '</div>';

		// Carousel nav
		echo '<a class="carousel-control left carousel-control-prev" data-target="#'.$id.'" href="#'.$id.'" role="button" data-slide="prev" aria-label="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span></a>';
		echo '<a class="carousel-control right carousel-control-next" data-target="#'.$id.'" href="#'.$id.'" role="button" data-slide="next" aria-label="next"><span class="carousel-control-next-icon" aria-hidden="true"></span></a>';
		echo '<span class="gp_blank_img" data-src="'.common::GetDir('/include/imgs/blank.gif').'" style="display:none"></span>';
		echo '</div></div>';


		return ob_get_clean();
	}


	static function DefaultContent($default_content,$type){
		if( !self::ContentKeyMatch($type) ){
			return $default_content;
		}

		$section = array();

		ob_start();
		$id = 'carousel_'.time();

		echo '<div id="'.$id.'" class="gp_twitter_carousel carousel slide">';
		echo '<div style="padding-bottom:30%">';
		echo '<ol class="carousel-indicators">';
		echo '<li class="active gp_to_remove"></li>';
		echo '</ol>';

		//<!-- Carousel items -->
		echo '<div class="carousel-inner">';
		echo '<div class="item carousel-item active gp_to_remove"><img/></div>';
		echo '</div>';

		//<!-- Carousel nav -->
		echo '<a class="carousel-control left carousel-control-prev" data-target="#'.$id.'" href="#'.$id.'" role="button" data-slide="prev" aria-label="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span></a>';
		echo '<a class="carousel-control right carousel-control-next" data-target="#'.$id.'" href="#'.$id.'" role="button" data-slide="next" aria-label="next"><span class="carousel-control-next-icon" aria-hidden="true"></span></a>';
		echo '<span class="gp_blank_img" data-src="'.common::GetDir('/include/imgs/blank.gif').'" style="display:none"></span>';
		echo '</div></div>';

		$section['gp_label'] = 'Bootstrap Carousel Gallery';
		$section['gp_color'] = '#8d3ee8';
		$section['content'] = ob_get_clean();
		$section['height'] = '30%';
		$section['auto_start'] = false;
		$section['alt_style'] = false;
		$section['interval_speed'] = 5000;
		return $section;
	}


	static function SaveSection($return, $section, $type){
		if( !self::ContentKeyMatch($type) ){
			return $return;
		}
		global $page;

		$_POST += array('auto_start'=>'','images'=>array());

		$page->file_sections[$section]['auto_start']		= ($_POST['auto_start'] == 'true');
		$page->file_sections[$section]['alt_style']			= ($_POST['alt_style'] == 'true');
		$page->file_sections[$section]['images']			= $_POST['images'];
		$page->file_sections[$section]['captions']			= $_POST['captions'];
		$page->file_sections[$section]['height']			= $_POST['height'];
		$page->file_sections[$section]['content_version']	= 2;

		if( isset($_POST['interval_speed']) && is_numeric($_POST['interval_speed']) ){
			$page->file_sections[$section]['interval_speed'] = $_POST['interval_speed'];
		}

		$page->file_sections[$section]['content'] = self::GenerateContent($page->file_sections[$section]);

		return true;
	}


	/**
	 * Make sure the .js and .css is available to admins
	 */
	static function GenerateContent_Admin(){
		global $addonFolderName, $page, $addonRelativeCode;
		self::AddComponents();
	}


	static function AddComponents(){
		global $addonFolderName, $page, $addonRelativeCode;
		static $done = false;

		if( $done ) return;

		if( version_compare(gpversion,'5.1','>=') ){
			common::LoadComponents( 'bootstrap4-carousel' );
		}else{
		if( version_compare(gpversion,'4.3b2','>=') ){
			common::LoadComponents( 'bootstrap3-carousel' );
		}else{
			common::LoadComponents( 'bootstrap-carousel' );
		}
		}
		$done = true;
	}


	static function InlineEdit_Scripts($scripts,$type){
		if( !self::ContentKeyMatch($type) ){
			return $scripts;
		}
		global $addonRelativeCode;
		$scripts[] = '/include/js/inline_edit/image_common.js';
		$scripts[] = '/include/js/inline_edit/gallery_edit_202.js';
		$scripts[] = $addonRelativeCode.'/gallery_options.js';
		$scripts[] = '/include/js/jquery.auto_upload.js';

		return $scripts;
	}
}