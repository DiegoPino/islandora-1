<?php

namespace Drupal\Tests\islandora\Kernel;

use Drupal\islandora\RdfBundleSolver\JsonldContextGenerator;

/**
 * Tests the Json-LD context Generator methods and simple integration.
 *
 * @group islandora
 * @coversDefaultClass \Drupal\islandora\RdfBundleSolver\JsonldContextGenerator
 */
class JsonldContextGeneratorTest extends IslandoraKernelTestBase {

  use FedoraContentTypeCreationTrait {
    createFedoraResourceContentType as drupalCreateFedoraContentType;
  }
  /**
   * The entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityBundleListenerInterface
   */
  protected $entityBundleListener;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The JsonldContextGenerator we are testing.
   *
   * @var \Drupal\islandora\RdfBundleSolver\JsonldContextGeneratorInterface
   */
  protected $theJsonldContextGenerator;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $mapping_config_name = "rdf.mapping.fedora_resource.test_bundle";
    $types = ['schema:Thing'];
    // Save bundle mapping config.
    $rdfMapping = rdf_get_mapping('fedora_resource', 'rdf_source')
      ->setBundleMapping(['types' => $types])
      ->save();
    // Test that config file was saved.
    $mapping_config = \Drupal::configFactory()->listAll('rdf.mapping.');
    // Just test code while debugging.
    $this->pass(var_dump($mapping_config));
    $this->pass(var_dump($rdfMapping));

    $this->theJsonldContextGenerator = new JsonldContextGenerator(
        $this->container->get('entity_field.manager'),
        $this->container->get('entity_type.bundle.info'),
        $this->container->get('entity_type.manager'),
        $this->container->get('cache.default'),
        $this->container->get('logger.channel.islandora')
      );

  }

  /**
   * @covers \Drupal\islandora\RdfBundleSolver\JsonldContextGenerator::getContext
   */
  public function testGetContext() {
    // Test with known asserts.
    $context = $this->theJsonldContextGenerator->getContext('fedora_resource.rdf_source');
    $context_as_array = json_decode($context, TRUE);

    $this->assertTrue(is_array($context_as_array), 'JSON-LD Context generated has correct structure for known Bundle');
    $this->assertTrue(strpos('{"@context":{"schema":"http://schema.org/"', (string) $context) !== FALSE, "JSON-LD Context generated contains the expected values for known Bundle");

  }

  /**
   * Tests Exception in case of no rdf type.
   *
   * @expectedException \Exception
   * @covers \Drupal\islandora\RdfBundleSolver\JsonldContextGenerator::getContext
   */
  public function testGetContextException() {
    // This should throw the expected Exception.
    $newFedoraEntity = $this->drupalCreateFedoraContentType();
    error_log(var_dump($newFedoraEntity->id(), TRUE));
    $this->theJsonldContextGenerator->getContext('fedora_resource.' . $newFedoraEntity->id());

  }

}
