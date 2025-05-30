# Automatic ALT Tags on Images (WordPress)

![Latest Version](https://img.shields.io/badge/release-v1.4-orange)
[![WordPress Version](https://img.shields.io/badge/wordpress-%3E%3D6.5-00749c)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.0-8892BF.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0.html)

## Overview
This WordPress PHP snippet automatically adds ALT tags to images in specified Gutenberg blocks before they are rendered on the frontend. It supports multiple image blocks, including those from GenerateBlocks, EditorsKit, and QI Blocks, as well as the core Gutenberg Image block.

**I am happy to release this plugin for FREE. But if this is helpful to you in any way, please consider donating via [Paypal](https://paypal.me/fabienbutazzi) or use the Sponsor links in the sidebar to support this work and future enhancements.**

## Features
- Automatically adds ALT tags for images in supported Gutenberg blocks.
- Supports multiple image blocks:
  - Core Gutenberg Image (`core/image`)
  - GenerateBlocks Image (`generateblocks/image`)
  - EditorsKit Image (`editorskit/image`)
  - EditorsKit Advanced Image (`editorskit/advanced-image`)
  - QI Blocks Media Image (`qi-blocks/media-image`)
  - QI Blocks Image Slider (`qi-blocks/image-slider`)
  - QI Blocks Image Gallery (`qi-blocks/image-gallery`)
- Can be easily extended to support other blocks, including custom ones

## Installation

### Option 1: Add to `functions.php`
1. Open your active theme's `functions.php` file (I recommend using a child theme or you will lose the functionality on the next theme update).
2. Copy and paste the provided code snippet into the file.
3. Save the changes.

### Option 2: Use a Code Snippet Plugin
1. Install and activate a code snippet plugin (I personally use **FluentSnippets** because it keeps snippets out of the WP database).
2. Create a new snippet.
3. Copy and paste the provided code snippet into the snippet editor.
4. Set the scope to `"Site-wide/Everywhere"` and save the changes.

## Usage

### How It Works
- The snippet hooks into the `render_block` filter to modify the content of specified Gutenberg blocks before rendering.
- It checks if the block is one of the target image blocks.
- If it is, it retrieves the image ID(s) from the block attributes.
- For each image ID, it fetches the ALT text from the post meta and updates the image tag with the ALT attribute.

### Extending the Snippet
To add support for additional image blocks:
1. Add the new block path to the `$target_blocks` array in the `tct_add_alt_tags` function.
2. Add a corresponding case to the `get_image_ids_from_block` function to determine how to retrieve the image ID(s) for the new block.

#### Example
```php
$target_blocks = [
    // Existing blocks...
    'new-plugin/image-block',
];

switch ($block_name) {
    // Existing cases...
    case 'new-plugin/image-block':
        if (isset($block['attrs']['imageID'])) {
            $image_ids[] = absint($block['attrs']['imageID']);
        }
        break;
}
```

## Notes
- Ensure that the image IDs are correctly specified in your block attributes. The attribute names may vary between different plugins.
- If an image already has an ALT tag, this snippet will not modify it.
- If you concatenate your code, you may want to comment the first line `declare(strict_types=1);`because the Strict declaration should be on on the first line of PHP files otherwise you will get an error.

## Credits
- **Bill Erickson**: Original inspiration for the core functionality (on [Code Snippets](https://snippetclub.com/automatic-alt-tags-on-images-in-wordpress/))
- **Qwen 2.5 Coder 32B**: Final steps to improve the original snippet: code health -> 8.85
- **Devstral**: Reduced code complexity and improved performance: code health -> 9.38

---

Feel free to contribute or report issues on this repository!
