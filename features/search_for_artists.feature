@api
Feature: Given I am an admin user, I should be able to search for artists.

  Scenario: An anonymous should not be able to search for artists
    Given I am an anonymous user
    When I make a request to search for artists with "sub"
    Then I should receive a 403 response

  Scenario: An logged in user without the administer blocks permission should not be able to search for artists
    Given users:
      | name     | mail          | pass     |
      | a_tester | test@test.com | password |
    And I am logged in as "a_tester" with "password"
    When I make a request to search for artists with "sub"
    Then I should receive a 403 response

  Scenario: An logged in user with the administer blocks permission should not be able to search for artists
    Given users:
      | name     | mail          | pass     | roles         |
      | a_tester | test@test.com | password | administrator |
    And I am logged in as "a_tester" with "password"
    When I make a request to search for artists with "sub"
    Then I should receive a 200 response
    And The response should contain the search results
