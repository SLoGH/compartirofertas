<?php

class Import_Theme_Media implements Import_Theme_Item
{

	public function import()
	{
		$uploads		= wp_upload_dir();
		$filepath		= $uploads['path'];
		$attach_ids		= array();
		$default_images	= array('th_default1.jpg',		// 0 draw-closer-to-jesus-christ
								'th_default2.jpg',		// 1 another-slideshow-post
								'th_default3.jpg',		// 2 custom-slideshow-for-posts
								'oneclickinstall.png',	// 3 one-click-install
								'responsive.png',		// 4 responsive-layout
								'slide_fullwidth.jpg',	// 5 full-width-slideshow
							);
		
		
		foreach ($default_images as $filename)
		{
			/*
			$def_image = array(
				"src" => get_template_directory() . "/images/" . $filename,
				"link" => "", "description" => "", "type" => "upload",
				"title" => "");
			*/
			$file = $filepath . "/" . $filename;
			if(file_exists(get_template_directory() . "/backend/dummy/images/" . $filename))
			{
				copy(get_template_directory() . "/backend/dummy/images/" . $filename, $file);
				
				$wp_filetype = wp_check_filetype(basename($file), null);
				$attachment = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => preg_replace('/\.[^.]+$/', '', basename($file)),
					'post_content' => '',
					'post_status' => 'inherit'
				);


				$attach_id = wp_insert_attachment($attachment, $file);

				$imagesize = getimagesize($file);
				
				$metadata					= array();
				$metadata['width']			= $imagesize[0];
				$metadata['height']			= $imagesize[1];
				list($uwidth, $uheight)		= wp_constrain_dimensions($metadata['width'], $metadata['height'], 128, 96);
				$metadata['hwstring_small'] = "height='$uheight' width='$uwidth'";
				$metadata['file']			= _wp_relative_upload_path($file);

				global $_wp_additional_image_sizes;

				foreach (get_intermediate_image_sizes() as $s)
				{
					$sizes[$s] = array('name' => '', 'width' => '', 'height' => '', 'crop' => FALSE);
					$sizes[$s]['name'] = $s;

					if (isset($_wp_additional_image_sizes[$s]['width']))
						$sizes[$s]['width'] = intval($_wp_additional_image_sizes[$s]['width']);
					else
						$sizes[$s]['width'] = get_option("{$s}_size_w");

					if (isset($_wp_additional_image_sizes[$s]['height']))
						$sizes[$s]['height'] = intval($_wp_additional_image_sizes[$s]['height']);
					else
						$sizes[$s]['height'] = get_option("{$s}_size_h");

					if (isset($_wp_additional_image_sizes[$s]['crop']))
						$sizes[$s]['crop'] = intval($_wp_additional_image_sizes[$s]['crop']);
					else
						$sizes[$s]['crop'] = get_option("{$s}_crop");
				}

				$sizes = apply_filters('intermediate_image_sizes_advanced', $sizes);
				set_time_limit(30);
				foreach ($sizes as $size => $size_data)
				{
					$metadata['sizes'][$size] = image_make_intermediate_size($file, $size['width'], $size['height'], $size['crop']);
				}

				apply_filters('wp_generate_attachment_metadata', $metadata, $attach_id);
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				$att_data = wp_generate_attachment_metadata($attach_id, $file);
				wp_update_attachment_metadata($attach_id, $att_data);

				$attach_ids[] = $attach_id;
			}
		}
		//set default image for all posts	 
		$args = array(
			"post_type" => array('post', 'page', Custom_Posts_Type_Gallery::POST_TYPE),
			"posts_per_page" => "-1"
		);

		if(isset($attach_ids[5]))
		{
			$all_query = new WP_Query($args);
			while ($all_query->have_posts()) : $all_query->the_post();
				set_post_thumbnail(get_the_ID(), $attach_ids[5]);
			endwhile;
		}

		//set default image for slides posts	 
		$args = array(
			"post_type"			=> array(Custom_Posts_Type_Slideshow::POST_TYPE),
			"posts_per_page"	=> "-1"
		);

		$slideshow_query = new WP_Query($args);
		/**
		 * SKIPs slug 
		 * youtube-vimeo-or-selfhosted-videos
		 * 925 million <br/>undernourished people<br/> in the world
		 * 
		 */
		while ($slideshow_query->have_posts()) :
			$slideshow_query->the_post();
			global $post;
			
			if($post && isset($post->post_name))
			{
				/*
				'th_default1.jpg',		// 0 draw-closer-to-jesus-christ
				'th_default2.jpg',		// 1 another-slideshow-post
				'th_default3.jpg',		// 2 custom-slideshow-for-posts
				'oneclickinstall.png',	// 3 one-click-install
				'responsive.png',		// 4 responsive-layout
				'slide_fullwidth.jpg',	// 5 full-width-slideshow
				 */
				switch($post->post_name)
				{
					case 'draw-closer-to-jesus-christ':
						set_post_thumbnail(get_the_ID(), $attach_ids[0]);
						break;
					case 'another-slideshow-post':
						set_post_thumbnail(get_the_ID(), $attach_ids[1]);
						break;
					case 'custom-slideshow-for-posts':
						set_post_thumbnail(get_the_ID(), $attach_ids[2]);
						break;
					case 'one-click-install':
						set_post_thumbnail(get_the_ID(), $attach_ids[3]);
						break;
					case 'responsive-layout':
						set_post_thumbnail(get_the_ID(), $attach_ids[4]);
						break;
					case 'full-width-slideshow':
						set_post_thumbnail(get_the_ID(), $attach_ids[5]);
						break;
					default:
						break;
				}
			}
		endwhile;
	}
}

?>
