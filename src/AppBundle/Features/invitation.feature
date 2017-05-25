@api-user-invitation
Feature: Handle user invitation via POST Request

  In order to send user invitation
  I need to have ROLE_SUPER_ADMIN

  Background:
    Given there are Users with the following details:
      | id | username | email          | password | firstName | lastName | gender | photo     | role            |
      | 1  | peter    | peter@test.com | testpass | Peter     | Johns    | m      | peter.jpg | ROLE_SUPER_ADMIN |
      | 2  | john     | john@test.org  | johnpass | John      | Dow      | m      | john.jpg  | ROLE_JUNIOR      |
    And there are oAuth Client with the following details:
      | redirect_uri             |
      | http://local.docker:3000 |
    And I set header "Content-Type" with value "application/json"

  Scenario: Send invitation failed, Access Denied for no ROLE_SUPER_ADMIN
    When I am successfully logged in with username: "john", and password: "johnpass"
    And the response should contain "access_token"
    Then I send a "POST" request to "/app_acceptance.php/api/users/invitations" with body:
      """
      {
        "email": "s@test.com",
        "team": 1
      }
      """
    Then the response code should be 403

  Scenario: Send invitation successfully
    When I am successfully logged in with username: "peter", and password: "testpass"
    And the response should contain "access_token"
    Then I send a "POST" request to "/app_acceptance.php/api/users/invitations" with body:
      """
      {
        "email": "s@luchianenco.com",
        "team": 1
      }
      """
    Then the response code should be 200
