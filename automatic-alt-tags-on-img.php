<?php

declare(strict_types=1);

/**
 * Snippet Name: Blocks AutoALT
 * Snippet URI: https://fabienb.blog
 * Description: This plugin automatically adds ALT tags to images in specified Gutenberg blocks.
 * Version: 1.2
 * Author: Fabien Butazzi, improved and validated by Qwen 2.5 Coder 32B and Gemma 3 27B
 * Author URI: https://fabienb.blog
 * Text Domain: fabienb
 */

add_filter('render_block', 'tct_add_alt_tags', 10, 2);

/**
 * Add ALT tags to images in target blocks.
 *
 * @param string $content The block content.
 * @param array $block The block data.
 * @return string Updated content with added ALT tags.
 */
function tct_add_alt_tags(string $content, array $block): string
{
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
function get_image_ids_from_block(array $block): array
{
    $block_name = $block['blockName'];
    $image_ids = [];

    return match ($block_name) {
        'generateblocks/image' => [$block['attrs']['mediaId'] ?? 0],
        'editorskit/image', 'editorskit/advanced-image' => [$block['attrs']['imageID'] ?? 0],
        'qi-blocks/media-image' => [$block['attrs']['mediaId'] ?? 0],
        'qi-blocks/image-slider', 'qi-blocks/image-gallery' => array_map(function ($image) {
            return $image['id'] ?? 0;
        }, $block['attrs']['images'] ?? []),
        default => [$block['attrs']['id'] ?? 0],
    };
}

/**
 * Update the image tag with the ALT attribute.
 *
 * @param string $content The block content.
 * @param int $id The image ID.
 * @param string $alt The alt text.
 * @return string Updated content.
 */
function update_image_with_alt_tag(string $content, int $id, string $alt): string
{
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
