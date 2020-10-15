<?php

/**
 * @file
 * Contains api_gateway.page.inc.
 *
 * Page callback for APIGateway entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for APIGateway templates.
 *
 * Default template: api_gateway.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_api_gateway(array &$variables) {
  // Fetch APIGateway Entity Object.
  $api_gateway = $variables['elements']['#api_gateway'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
