@api-team
Feature: Handle CRUD operation over Team Document

  In order to send CRUD operation over Team Document
  I need to have ROLE_SUPER_ADMIN

  Background:
    Given there are Users with the following details:
      | id | username | email          | password | firstName | lastName | gender | photo     | role             |
      | 1  | peter    | peter@test.com | testpass | Peter     | Johns    | m      | peter.jpg | ROLE_SUPER_ADMIN |
      | 2  | john     | john@test.org  | johnpass | John      | Dow      | m      | john.jpg  | ROLE_JUNIOR      |
    And there are oAuth Client with the following details:
      | redirect_uri             |
      | http://local.docker:3000 |
    And I set header "Content-Type" with value "application/json"

  Scenario: Create Team
    When I am successfully logged in with username: "peter", and password: "testpass"
    Then the response code should be 200
    And the response should contain "access_token"
    Then I send a "POST" request to "/app_acceptance.php/api/teams" with body:
      """
      {
        "title": "TestTeam"
      }
      """
    And the response code should be 201