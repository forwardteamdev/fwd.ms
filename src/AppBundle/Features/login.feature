@api-user-login
Feature: Handle user login via the RESTful API

  In order to allow secure access to the system
  I need to be able to let users log in and out

  Background:
    Given there are Users with the following details:
      | id | username | email          | password | firstName | lastName | gender | photo     | role            |
      | 1  | peter    | peter@test.com | testpass | Peter     | Johns    | m      | peter.jpg | ROLE_SUPER_ADMIN |
      | 2  | john     | john@test.org  | johnpass | John      | Dow      | m      | john.jpg  | ROLE_JUNIOR      |
    And there are oAuth Client with the following details:
      | redirect_uri             |
      | http://local.docker:3000 |
    And I set header "Content-Type" with value "application/json"


  Scenario: User can Login with good credentials
    When I send a "POST" request to "/app_acceptance.php/oauth/v2/token" with body:
      """
      {
        "grant_type": "password",
        "client_id": "__OAUTH_CLIENT_ID__",
        "client_secret":"__OAUTH_CLIENT_SECRET__",
        "username": "peter",
        "password": "testpass"
      }
      """
    Then the response code should be 200
    And the response should contain "access_token"

  Scenario: User is successfully logged in
    When I am successfully logged in with username: "peter", and password: "testpass"
    Then the response code should be 200
    And the response should contain "access_token"
    Then I send a "GET" request to "/app_acceptance.php/api/user"
    And the response code should be 200
    And the response should contain "email"
