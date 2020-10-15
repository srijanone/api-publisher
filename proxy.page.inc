<?php

/**
 * @file
 * Contains proxy.page.inc.
 *
 * Page callback for Proxy entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Proxy templates.
 *
 * Default template: proxy.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_proxy(array &$variables) {
  // Fetch Proxy Entity Object.
  $proxy = $variables['elements']['#proxy'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
