<?php

namespace Drupal\utility\TwigExtension;

use Drupal\Core\Render\Renderer;
use Drupal\Core\Template\TwigExtension;
use Drupal\Core\Theme\ThemeManager;
use Drupal\Core\Theme\Registry;

/**
 * Class UtilityTwigExtension.
 *
 * @package Drupal\utility
 */
class UtilityTwigExtension extends TwigExtension {

  /**
   * Drupal\Core\Render\Renderer definition.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * @var \Drupal\Core\Theme\Registry
   */
  protected $themeRegistry;

  /**
   * @var \Drupal\Core\Theme\ThemeManager
   */
  protected $themeManager;

  /**
   * All theme templates from current theme.
   * @var array
   */
  protected $templates;

  /**
   * UtilityTwigExtension constructor.
   * @param \Drupal\Core\Render\Renderer $renderer
   * @param \Drupal\Core\Theme\ThemeManager $theme_manager
   * @param \Drupal\Core\Theme\Registry $theme_registry
   */
  public function __construct(Renderer $renderer, ThemeManager $theme_manager, Registry $theme_registry) {
    parent::__construct($renderer);
    $this->themeManager = $theme_manager;
    $this->themeRegistry = $theme_registry;
    $this->templates = drupal_find_theme_templates($this->themeRegistry->get(), '.html.twig', $this->themeManager->getActiveTheme()->getPath());
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'utility.twig.extension';
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('template_path', [$this, 'templatePath']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getTests() {
    return [
      new \Twig_SimpleTest('ondisk', [$this, 'onDisk']),
    ];
  }

  /**
   * Verify if given template name exists on active theme.
   * @param $template String template name to check.
   * @return boolean
   *   TRUE when template already exists, otherwise false.
   */
  public function onDisk($template) {
    // Value should be string value.
    $template = (is_string($template)) ? $template : '';
    // Get template machine name.
    $key = str_replace('-', '_', $template);
    // Check if current template exists.
    return array_key_exists($key, $this->templates);
  }

  /**
   * Get path to given template name.
   * @param $template string Template name to check.
   * @return string
   *   Path to template, otherwise NULL.
   */
  public function templatePath($template){
    // Check that current template already exists.
    if($this->onDisk($template)) {
      // Value should be string value.
      $template = (is_string($template)) ? $template : '';
      // Get template machine name.
      $key = str_replace('-', '_', $template);
      // Return path template.
      return base_path() . $this->templates[$key]['path'] . '/';
    }
    // If it fails then return NULL.
    return NULL;
  }
}
