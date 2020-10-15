<?php

/**
 * @file
 * Contains open_api_spec.page.inc.
 *
 * Page callback for OpenAPI Specification entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for OpenAPI Specification templates.
 *
 * Default template: open_api_spec.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_open_api_spec(array &$variables) {
  // Fetch OpenAPISpec Entity Object.
  $open_api_spec = $variables['elements']['#open_api_spec'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
