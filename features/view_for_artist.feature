@api
Feature: Given I am a user, I should be able to view an artists page.

  Scenario: An user with the view spotify content permission should be able to view a valid artist's page
    Given I am logged in as a user with the "view spotify content" permission
    When I visit "view-artist/1234"
    Then I should see the text "High Contrast"
    And I should see the text "drum & bass"
    And I should not see the text "artist with id: 1234 not found"

  Scenario: An user with the view spotify content permission should not be able to view an invalid artist page
    Given I am logged in as a user with the "view spotify content" permission
    When I visit "view-artist/12345"
    Then I should not see the text "High Contrast"
    And I should not see the text "drum & bass"
    And I should see the text "artist with id: 12345 not found"

  Scenario: An anonymous without the view spotify content permission should not be able to view an invalid artist page
    Given I am an anonymous user
    When I go to "view-artist/12345"
    Then I should get a 403 HTTP response
