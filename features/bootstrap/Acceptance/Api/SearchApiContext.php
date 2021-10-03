<?php

namespace Acceptance\Api;

use Drupal\Core\Entity\EntityInterface;
use Imbo\BehatApiExtension\Context\ApiContext;
use PHPUnit\Framework\Assert;

class SearchApiContext extends ApiContext {

  private $cookie;

  /**
   * @var \Drupal\Core\Entity\EntityInterface
   */
  private $user;

  /**
   * @Given I am logged in as :username with :password
   */
  public function iAmLoggedInAsWith($username, $password) {
    $this->setDefaultRequestHeaders();

    $payload = json_encode([
      'name' => $username,
      'pass' => $password,
    ]);

    $this->setRequestBody($payload);

    $this->setRequestMethod('POST');
    $this->setRequestPath('/user/login?_format=json');
    $this->sendRequest();

    $cookie = $this->response->getHeader('set-cookie');
    $this->cookie = trim(explode(';', $cookie[0])[0]);
    $this->user = $this->loadUserByName($username);
  }

  /**
   * @When I make a request to search for artists with :searchQuery
   */
  public function iMakeARequestToSearchForArtistsWith(string $searchQuery) {

    $this->setDefaultRequestHeaders();
    $this->setAuthHeader();

    $this->setRequestMethod('GET');
    $this->setRequestPath(sprintf('/find-artists?q=%s', $searchQuery));

    $this->sendRequest();
  }

  /**
   * @Then I should receive a :statusCode response
   */
  public function iShouldReceiveAResponse(int $statusCode) {
    $this->assertResponseCodeIs($statusCode);
  }

  /**
   * @Then The response should contain the search results
   */
  public function theResponseShouldContainTheSearchResults()
  {
    $response = $this->response->getBody()->getContents();

    $responseArray = json_decode($response, true);
    $expected = [
      [
        'value' => 'Sub Focus - 1234',
        'label' => 'Sub Focus - 1234',
      ],
    ];
    Assert::assertSame($expected, $responseArray);
  }

  private function setDefaultRequestHeaders(): void {
    $this->setRequestHeader('Content-Type', 'application/json');
  }

  private function setAuthHeader() {
    if ($this->cookie) {
      $parts = explode('=', $this->cookie);
      $this->setRequestHeader($parts[0], $parts[1]);
    }
  }

  private function loadUserByName(string $name): EntityInterface {
    $entity_type_manager = \Drupal::entityTypeManager();
    $storage = $entity_type_manager->getStorage('user');
    $entities = $storage->loadByProperties([
      'name' => $name,
    ]);

    if (empty($entities)) {
      throw new \RuntimeException(sprintf('user %s does not exist', $name));
    }

    return reset($entities);
  }

}
