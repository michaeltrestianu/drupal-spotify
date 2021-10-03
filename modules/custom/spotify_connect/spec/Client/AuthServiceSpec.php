<?php

namespace spec\Drupal\spotify_connect\Client;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\TempStore\PrivateTempStore;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\spotify_connect\Client\AuthService;
use Drupal\spotify_connect\Client\Response\Exception\InvalidResponseException;
use GuzzleHttp\ClientInterface;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class AuthServiceSpec extends ObjectBehavior {

  function it_is_initializable() {
    $this->shouldHaveType(AuthService::class);
  }

  function let(
    ClientInterface $client,
    PrivateTempStoreFactory $privateTempStoreFactory,
    ConfigFactoryInterface $config,
    ImmutableConfig $spotifyConfig,
    ResponseInterface $response
  ) {
    $spotifyConfig->get('auth_uri')->willReturn('auth-endpoint');
    $spotifyConfig->get('client_id')->willReturn('123');
    $spotifyConfig->get('client_secret')->willReturn('123');
    $config->get('spotify.settings')->willReturn($spotifyConfig);

    $client->request(
      'POST',
      'auth-endpoint',
      [
        'headers' => [
          'Authorization' => 'Basic MTIzOjEyMw==',
          'Content-Type' => 'application/x-www-form-urlencoded',
        ],
        'form_params' => [
          'grant_type' => 'client_credentials',
        ],
      ]
    )->willReturn($response);

    $this->beConstructedWith($client, $privateTempStoreFactory, $config);
  }

  function it_throws_an_invalid_response_exception_if_the_response_is_not_http_ok(
    ResponseInterface $response
  ) {
    $response->getStatusCode()->willReturn(400);
    $this->shouldThrow(InvalidResponseException::class)->duringAuthenticate();
  }

  function it_throws_a_runtime_exception_if_the_response_body_cannot_be_decoded(
    ResponseInterface $response,
    StreamInterface $stream
  ) {
    $stream->getContents()->willReturn('{{{{{');
    $response->getStatusCode()->willReturn(200);
    $response->getBody()->willReturn($stream);
    $this->shouldThrow(\RuntimeException::class)->duringAuthenticate();
  }

  function it_stores_the_access_token_in_private_temp_storage(
    ResponseInterface $response,
    StreamInterface $stream,
    PrivateTempStoreFactory $privateTempStoreFactory,
    PrivateTempStore $privateTempStore
  ) {
    $privateTempStore->set('access_token', '12345')->shouldBeCalled();
    $privateTempStoreFactory->get('spotify_connect')->willReturn($privateTempStore);
    $stream->getContents()->willReturn('{"access_token": "12345"}');
    $response->getStatusCode()->willReturn(200);
    $response->getBody()->willReturn($stream);
    $stream->close()->shouldBeCalled();
    $this->authenticate();
  }

}
