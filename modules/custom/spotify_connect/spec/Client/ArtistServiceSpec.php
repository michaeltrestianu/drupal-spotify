<?php

namespace spec\Drupal\spotify_connect\Client;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\spotify_connect\Client\ArtistService;
use Drupal\spotify_connect\Client\AuthService;
use Drupal\spotify_connect\Client\Data\Artist;
use Drupal\spotify_connect\Client\Data\Image;
use Drupal\spotify_connect\Provider\BearerTokenProvider;
use GuzzleHttp\ClientInterface;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ArtistServiceSpec extends ObjectBehavior {

  function it_is_initializable() {
    $this->shouldHaveType(ArtistService::class);
  }

  function let(
    ClientInterface $client,
    LoggerChannelFactoryInterface $loggerChannelFactory,
    LoggerChannelInterface $spotifyLoggerChannel,
    AuthService $authService,
    BearerTokenProvider $bearerTokenProvider,
    ResponseInterface $response,
    StreamInterface $stream
  ) {
    $loggerChannelFactory->get('spotify_service')->willReturn(
      $spotifyLoggerChannel
    );
    $response->getBody()->willReturn($stream);
    $stream->getContents()->willReturn(
      '{"name": "Sub Focus","id": "123456","genres": ["dancefloor dnb"],"images": [{"height": 640, "url": "url", "width": 640}]}'
    );
    $client->request(
      'GET',
      'artists/123456',
      [
        'headers' => [
          'Authorization' => 'Bearer 123',
        ],
      ]
    )->willReturn($response);

    $this->beConstructedWith(
      $client,
      $loggerChannelFactory,
      $authService,
      $bearerTokenProvider
    );
  }

  function it_logs_the_invalid_response_exception_if_the_response_is_not_http_ok(
    ResponseInterface $response,
    BearerTokenProvider $bearerTokenProvider,
    LoggerChannelInterface $spotifyLoggerChannel
  ) {
    $bearerTokenProvider->get()->willReturn('123');
    $response->getStatusCode()->willReturn(400);
    $spotifyLoggerChannel->error(
      'An error occurred fetching artists data %error',
      ['%error' => 'Non 200 response received (400), please check request or connection.artists/123456']
    )->shouldBeCalled();

    $this->getArtistById('123456')->shouldBeNull();
  }

  function it_makes_a_successful_request_to_get_an_artist_with_a_valid_bearer_token(
    BearerTokenProvider $bearerTokenProvider,
    ResponseInterface $response,
    StreamInterface $stream
  ) {
    $bearerTokenProvider->get()->willReturn('123');
    $response->getStatusCode()->willReturn(200);
    $stream->close()->shouldBeCalled();
    $this->getArtistById('123456')->shouldBeLike(
      new Artist(
        'Sub Focus',
        '123456',
        [
          'dancefloor dnb',
        ],
        [
          new Image(640, 640, 'url'),
        ]
      )
    );
  }

  function it_authenticates_if_there_is_an_empty_bearer_token(
    BearerTokenProvider $bearerTokenProvider,
    AuthService $authService,
    ResponseInterface $response,
    StreamInterface $stream
  ) {
    $bearerTokenProvider->get()->willReturn(NULL, '123');
    $response->getStatusCode()->willReturn(200);
    $stream->close()->shouldBeCalled();
    $this->getArtistById('123456')->shouldBeLike(
      new Artist(
        'Sub Focus',
        '123456',
        [
          'dancefloor dnb',
        ],
        [
          new Image(640, 640, 'url'),
        ]
      )
    );
    $authService->authenticate()->shouldHaveBeenCalled();
  }

  function it_re_authenticates_if_the_current_bearer_token_has_expired(
    BearerTokenProvider $bearerTokenProvider,
    AuthService $authService,
    ResponseInterface $response,
    StreamInterface $stream
  ) {
    $response->getStatusCode()->willReturn(401, 200);
    $bearerTokenProvider->get()->willReturn('123');
    $stream->close()->shouldBeCalled();
    $this->getArtistById('123456')->shouldBeLike(
      new Artist(
        'Sub Focus',
        '123456',
        [
          'dancefloor dnb',
        ],
        [
          new Image(640, 640, 'url'),
        ]
      )
    );
    $authService->authenticate()->shouldHaveBeenCalled();
  }

  function it_logs_an_error_if_the_response_body_cannot_be_decoded(
    ResponseInterface $response,
    BearerTokenProvider $bearerTokenProvider,
    StreamInterface $stream,
    LoggerChannelInterface $spotifyLoggerChannel
  ) {
    $bearerTokenProvider->get()->willReturn('123');
    $stream->getContents()->willReturn('{{{{{');
    $response->getStatusCode()->willReturn(200);
    $spotifyLoggerChannel->error(
      'Unable to decode response body when requesting artist'
    )->shouldBeCalled();
    $this->getArtistById('123456')->shouldBeNull();
  }

}
