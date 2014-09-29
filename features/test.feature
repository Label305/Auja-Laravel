Feature: test
  In order to test behat
  As Niek
  I want to test

  Scenario: Perform a test
    Given I have a model "Club"
    And it has the following columns:
      | column | type    |
      | id     | integer |
      | name   | string  |
    And I have a model "Team"
    And it has the following columns:
      | column  | type    |
      | id      | integer |
      | name    | string  |
      | club_id | integer |
    When I configure the Auja Configurator
    Then I should get a configuration with the following models:
      | name |
      | Club |
      | Team |
    And it should have a belongs to relationship between:
      | left | right |
      | Team | Club  |
