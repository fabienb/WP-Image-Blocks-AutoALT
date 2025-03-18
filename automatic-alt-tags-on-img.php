<?php
declare(strict_types=1);

/**
 * Plugin Name: Blocks AutoALT
 * Plugin URI: https://fabienb.blog
 * Description: This plugin automatically adds ALT tags to images in specified Gutenberg blocks.
 * Version: 1.1
 * Author: Fabien Butazzi, improved and validated by Qwen 2.5 Coder 32B and Gemma 3 27B
 * Author URI: https://fabienb.blog
 * Text Domain: fabienb
 */

add_filter('render_block', 'tct_add_alt_tags', 10, 2);

function tct_add_alt_tags($content, $block) {
	// Get the block name
	$block_name = $block['blockName'];

	// Define target blocks that need ALT tags to be added
	$target_blocks = [
		'core/image',
		'generateblocks/image',
		// EditorsKit
		'editorskit/image',
		'editorskit/advanced-image',
		// QI Blocks
		'qi-blocks/media-image',
		'qi-blocks/image-slider',
		'qi-blocks/image-gallery', // Add more if needed
	];

	// Check if the current block is one of the target blocks
	if (!in_array($block_name, $target_blocks)) {
		return $content;
	}

	// Determine the image ID(s) based on the block type
	$image_ids = get_image_ids_from_block($block);

	// Iterate over each image ID and add ALT tags
	foreach ($image_ids as $id) {
		if ($id !== 0) {
			$alt = get_post_meta($id, '_wp_attachment_image_alt', true);
			if (!empty($alt)) {
				$content = update_image_with_alt_tag($content, $id, $alt);
			}
		}
	}

	return $content;
}

/**
 * Determine the image IDs based on the block type.
 *
 * @param array $block The block data.
 * @return array Image IDs.
 */
function get_image_ids_from_block($block) {
	$block_name = $block['blockName'];
	$image_ids = [];

	switch ($block_name) {
		case 'generateblocks/image':
			if (isset($block['attrs']['mediaId'])) {
				$image_ids[] = absint($block['attrs']['mediaId']);
			}
			break;
		case 'editorskit/image':
		case 'editorskit/advanced-image':
			if (isset($block['attrs']['imageID'])) {
				$image_ids[] = absint($block['attrs']['imageID']);
			}
			break;
		case 'qi-blocks/media-image':
			if (isset($block['attrs']['mediaId'])) {
				$image_ids[] = absint($block['attrs']['mediaId']);
			}
			break;
		case 'qi-blocks/image-slider':
		case 'qi-blocks/image-gallery':
			if (isset($block['attrs']['images']) && is_array($block['attrs']['images'])) {
				foreach ($block['attrs']['images'] as $image) {
					if (isset($image['id'])) {
						$image_ids[] = absint($image['id']);
					}
				}
			}
			break;
		default:
			if (isset($block['attrs']['id'])) {
				$image_ids[] = absint($block['attrs']['id']);
			}
			break;
	}

	return $image_ids;
}

/**
 * Update the image tag with the ALT attribute.
 *
 * @param string $content The block content.
 * @param int $id The image ID.
 * @param string $alt The alt text.
 * @return string Updated content.
 */
function update_image_with_alt_tag($content, $id, $alt) {
	// Escape the alt text to ensure it's safe for output
	$escaped_alt = esc_attr($alt);

	// Get the image URL from the ID
	$image_url = wp_get_attachment_url($id);

	if (!$image_url) {
		return $content;
	}

	// Check if the image tag already has an empty alt attribute and replace it
	if (false !== strpos($content, 'src="' . $image_url . '"')) {
		if (false !== strpos($content, 'alt=""')) {
			$content = str_replace('alt=""', 'alt="' . $escaped_alt . '"', $content);
		} elseif (false === strpos($content, 'alt="')) {
			$content = str_replace('src="' . $image_url . '"', 'alt="' . $escaped_alt . '" src="' . $image_url . '"', $content);
		}
	}

	return $content;
}
