<?php
/**
 * Plugin Name: Dandy Plugins
 * Description: A collection of Gutenberg blocks built on top of ACF.
 * Version: 1.1.0
 * Author: First+Third
 * Author URI: https://firstandthird.com
 * Requires at least: 5.9.0
 * Requires PHP: 7
 */

namespace FirstAndThird\Dandy;

class Dandy_Blocks {
  static $plugin_path = '';
  static $theme_path = '';
  static $options = [];
  static $theme_options = [];
  static $category = 'dandy-blocks';
  static $theme_category = 'custom-blocks';
  static $name = 'Dandy Blocks';
  static $theme_name = 'Custom Blocks';

  static function init() {
    self::$plugin_path = plugin_dir_path(__FILE__);
    self::$theme_path = get_stylesheet_directory();

    $config = file_get_contents(self::$plugin_path . 'config.json');

    self::$options = json_decode($config, true);

    if (file_exists(self::$theme_path . '/blocks.json')) {
      $theme_config = file_get_contents(self::$theme_path . '/blocks.json');

      self::$theme_options = json_decode($theme_config, true);
    }

    add_filter('acf/settings/load_json', ['FirstAndThird\Dandy\Dandy_Blocks', 'register_acf_fields_path']);
    add_filter('block_categories_all', ['FirstAndThird\Dandy\Dandy_Blocks', 'register_block_category'], 10, 2 );
    add_action('init', ['FirstAndThird\Dandy\Dandy_Blocks', 'register_blocks']);
  }

  static function log($message) {
    if (!WP_DEBUG) {
      return;
    }

    //phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r,WordPress.PHP.DevelopmentFunctions.error_log_error_log --  This is only run in development
    if (is_array($message) || is_object($message)) {
      error_log(print_r($message, true));
      return;
    }

    error_log($message);
    //phpcs:enable
  }

  static function register_acf_fields_path($paths) {
    $paths[] = self::$plugin_path . 'acf-fields';

    return $paths;
  }

  static function register_block_category($categories) {
    return array_merge(
      $categories,
      [
        [
          'slug' => self::$category,
          'title' => self::$name,
          'icon'  => '<svg height="100" width="100" xmlns="http://www.w3.org/2000/svg"><path d="M68.154 10a.991.991 0 00-.352.067c-.022.008-.04.024-.061.034a.99.99 0 00-.247.158c-.011.01-.026.013-.037.024-.05.049-5.2 4.902-17.457 7.232-12.258-2.33-17.406-7.183-17.457-7.232-.011-.011-.026-.014-.037-.024a.988.988 0 00-.233-.15c-.027-.013-.05-.033-.078-.044a.992.992 0 00-.349-.065c-.41 0-10.182.086-21.404 7.623a1 1 0 101.116 1.66c7.726-5.19 14.832-6.68 18.214-7.109L17.653 28.218a1 1 0 00-.018 1.18l7.532 10.635-6.587 6.91a1 1 0 00-.08 1.286l30.697 41.367a1 1 0 001.606 0L81.5 48.229a1 1 0 00-.08-1.286l-6.587-6.91L82.365 29.4a1 1 0 00-.018-1.18L70.225 12.17c3.374.423 10.466 1.907 18.217 7.112a1 1 0 101.115-1.66C78.337 10.086 68.565 10 68.155 10zM47.24 18.982l-11.01 6.394-2.782-11.909c2.424 1.588 6.858 3.915 13.792 5.515zm-6.752 6.234c1.708 2.603 3.8 4.085 4.952 4.76l-5.092 13.036-3.643-15.6 3.783-2.196zM20.608 47.714l6.575-6.897a1 1 0 00.092-1.268l-7.585-10.71 11.683-15.467 16.648 71.283-27.413-36.941zM49 80.054l-7.847-33.601 6.474-16.577c.028-.07.026-.14.037-.211.008-.053.028-.103.027-.155 0-.08-.023-.155-.042-.231-.011-.048-.013-.098-.032-.143-.028-.07-.076-.13-.12-.192-.03-.043-.052-.09-.09-.129-.05-.05-.115-.084-.176-.124-.046-.03-.081-.071-.133-.094-.03-.014-2.786-1.261-4.877-4.387L49 20.273v59.781zm17.55-66.587l-2.78 11.909-11.01-6.394c6.933-1.6 11.367-3.927 13.79-5.515zm-3.256 13.946L59.65 43.012l-5.092-13.035c1.151-.676 3.244-2.158 4.952-4.761l3.783 2.197zM51 20.273l6.78 3.937c-2.092 3.126-4.848 4.373-4.878 4.387-.055.024-.094.067-.143.1-.056.038-.118.069-.165.116-.045.046-.073.103-.108.156-.035.054-.078.102-.102.162-.025.058-.029.122-.042.184-.013.063-.034.124-.034.19-.001.064.021.128.033.193.012.06.009.12.032.178l6.474 16.577L51 80.054V20.273zm29.31 8.567l-7.585 10.709a1 1 0 00.092 1.268l6.575 6.897-27.413 36.94 16.648-71.282L80.31 28.84z"/></svg>'
        ],
        [
          'slug' => self::$theme_category,
          'title' => self::$theme_name
        ]
      ]
    );
  }

  static function register_blocks() {
    if (!isset(self::$options['blocks'])) {
      self::log('Dandy Blocks has incorrect configuration.');
      return;
    }

    if (!function_exists('acf_register_block_type')) {
      self::log('ACF not available. Dandy Blocks requires ACF to be enabled.');
      return;
    }

    foreach (self::$options['blocks'] as $block_name => $block) {
      acf_register_block_type([
        'name' => $block_name,
        'title' => $block['title'],
        'description' => $block['description'] ?? '',
        'icon' => $block['icon'] ?? 'dashicons-editor-help',
        'keywords' => is_array($block['keywords']) ? array_merge(['dandy'], $block['keywords']) : ['dandy'],
        'supports' => array_merge([
          'align' => false,
          'align_text' => false,
          'align_content' => false,
          'anchor' => true,
          'mode' => true,
          'multiple' => true
        ], $block['supports'] ?? []),
        'example' => $block['example'] ?? [],
        'category' => $block['category'] ?? self::$category,
        'render_template' => self::$plugin_path . 'blocks/' . $block_name . '/block.php'
      ]);
    }

    // load theme blocks
    if (!empty(self::$theme_options['blocks'])) {

      foreach (self::$theme_options['blocks'] as $block_name => $block) {
        acf_register_block_type([
          'name' => $block_name,
          'title' => $block['title'],
          'description' => $block['description'] ?? '',
          'icon' => $block['icon'] ?? 'dashicons-editor-help',
          'keywords' => is_array($block['keywords']) ? array_merge(['dandy'], $block['keywords']) : ['dandy'],
          'supports' => array_merge([
            'align' => false,
            'align_text' => false,
            'align_content' => false,
            'mode' => true,
            'multiple' => true
          ], $block['supports'] ?? []),
          'example' => $block['example'] ?? [],
          'category' => $block['category'] ?? self::$theme_category,
          'render_template' => self::$theme_path . '/blocks/' . $block_name . '/block.php'
        ]);
      }
    }
  }
}

Dandy_Blocks::init();
