<?php

namespace spec\Drupal\spotify_connect\Client;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\spotify_connect\Client\AuthService;
use Drupal\spotify_connect\Client\Data\Artist;
use Drupal\spotify_connect\Client\FindArtistsRequest;
use Drupal\spotify_connect\Client\Response\Artists;
use Drupal\spotify_connect\Client\SearchService;
use Drupal\spotify_connect\Provider\BearerTokenProvider;
use GuzzleHttp\ClientInterface;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class SearchServiceSpec extends ObjectBehavior {

  function it_is_initializable() {
    $this->shouldHaveType(SearchService::class);
  }

  function let(
    ClientInterface $client,
    LoggerChannelFactoryInterface $loggerChannelFactory,
    LoggerChannelInterface $spotifyLoggerChannel,
    AuthService $authService,
    BearerTokenProvider $bearerTokenProvider,
    ResponseInterface $response,
    FindArtistsRequest $findArtistsRequest,
    StreamInterface $stream
  ) {
    $loggerChannelFactory->get('spotify_service')->willReturn(
      $spotifyLoggerChannel
    );
    $response->getBody()->willReturn($stream);
    $findArtistsRequest->getQuery()->willReturn(
      'limit=20&q=sub+focus&type=artist'
    );

    $stream->getContents()->willReturn('{"artists":{"items":[{"name":"Sub Focus","id":"123456"}],"total":1}}');

    $client->request(
      'GET',
      'search',
      [
        'query' => 'limit=20&q=sub+focus&type=artist',
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

  function it_returns_an_empty_artists_response_if_there_is_no_query(
    FindArtistsRequest $findArtistsRequest
  ) {
    $findArtistsRequest->getQuery()->willReturn('');
    $this->searchArtist($findArtistsRequest)->shouldBeLike(new Artists([], 0));
  }

  function it_logs_the_invalid_response_exception_if_the_response_is_not_http_ok(
    ResponseInterface $response,
    BearerTokenProvider $bearerTokenProvider,
    LoggerChannelInterface $spotifyLoggerChannel,
    FindArtistsRequest $findArtistsRequest
  ) {
    $bearerTokenProvider->get()->willReturn('123');
    $response->getStatusCode()->willReturn(400);
    $spotifyLoggerChannel->error(
      'An error occurred fetching artists data %error',
      ['%error' => 'Non 200 response received (400), please check request or connection.']
    )->shouldBeCalled();

    $this->searchArtist($findArtistsRequest)->shouldBeLike(new Artists([], 0));
  }

  function it_logs_an_error_if_the_response_body_cannot_be_decoded(
    ResponseInterface $response,
    BearerTokenProvider $bearerTokenProvider,
    StreamInterface $stream,
    LoggerChannelInterface $spotifyLoggerChannel,
    FindArtistsRequest $findArtistsRequest
  ) {
    $bearerTokenProvider->get()->willReturn('123');
    $stream->getContents()->willReturn('{{{{{');
    $response->getStatusCode()->willReturn(200);
    $spotifyLoggerChannel->error(
      'Unable to decode response body when requesting artists'
    )->shouldBeCalled();
    $this->searchArtist($findArtistsRequest)->shouldBeLike(new Artists([], 0));
  }

  function it_successfully_retrieves_artists_with_a_valid_bearer_token(
    ResponseInterface $response,
    BearerTokenProvider $bearerTokenProvider,
    StreamInterface $stream,
    FindArtistsRequest $findArtistsRequest
  ) {
    $bearerTokenProvider->get()->willReturn('123');
    $response->getStatusCode()->willReturn(200);
    $stream->close()->shouldBeCalled();
    $this->searchArtist($findArtistsRequest)->shouldBeLike(
      new Artists([
        new Artist('Sub Focus', '123456', [], []),
      ], 1)
    );
  }

  function it_authenticates_if_there_is_an_empty_bearer_token(
    BearerTokenProvider $bearerTokenProvider,
    AuthService $authService,
    ResponseInterface $response,
    StreamInterface $stream,
    FindArtistsRequest $findArtistsRequest
  ) {
    $bearerTokenProvider->get()->willReturn(NULL, '123');
    $response->getStatusCode()->willReturn(200);
    $stream->close()->shouldBeCalled();
    $this->searchArtist($findArtistsRequest)->shouldBeLike(
      new Artists([
        new Artist('Sub Focus', '123456', [], []),
      ], 1)
    );
    $authService->authenticate()->shouldHaveBeenCalled();
  }

  function it_re_authenticates_if_the_current_bearer_token_has_expired(
    BearerTokenProvider $bearerTokenProvider,
    AuthService $authService,
    ResponseInterface $response,
    StreamInterface $stream,
    FindArtistsRequest $findArtistsRequest
  ) {
    $response->getStatusCode()->willReturn(401, 200);
    $bearerTokenProvider->get()->willReturn('123');
    $stream->close()->shouldBeCalled();
    $this->searchArtist($findArtistsRequest)->shouldBeLike(
      new Artists([
        new Artist('Sub Focus', '123456', [], []),
      ], 1)
    );
    $authService->authenticate()->shouldHaveBeenCalled();
  }

}
