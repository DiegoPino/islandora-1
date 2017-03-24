<?php

namespace Drupal\Tests\islandora\Kernel;
use \Drupal\islandora\Entity\FedoraResourceType;

trait FedoraContentTypeCreationTrait {

  /**
   * Creates a custom content Fedora Resource type based on default settings.
   *
   * @param array $values
   *   An array of settings to change from the defaults.
   *   Example: 'type' => 'foo'.
   *
   * @return \Drupal\islandora\Entity\FedoraResourceType
   *   Created content type.
   */
  protected function createFedoraResourceContentType(array $values = array()) {
    // Find a non-existent random type name.
    if (!isset($values['type'])) {
      do {
        $id = strtolower(new \Drupal\Component\Utility\Random(8));
      } while (FedoraResourceType::load($id));
    }
    else {
      $id = $values['type'];
    }
    $values += array(
      'type' => $id,
      'name' => $id,
    );
    $type = FedoraResourceType::create($values);
    $type->save();
    return $type;
  }

}
