@api-user-registration
Feature: Handle user registration via POST Request

  In order to register new user
  I need to feel the registration form

  Background:
    Given there are User Invitations with the following details:
      | email       | team |
      | s@test.com  |  1   |
      | l@test.com  |  1   |
    And I set header "Content-Type" with value "application/json"

  Scenario: User Registration
    When I have user invitation code for email: "s@test.com"
    And I send a "POST" request to "/app_acceptance.php/users/registrations" with body:
      """
      {
        "username": "s@test.com",
        "email": "s@test.com",
        "plainPassword": {
          "first": "123",
          "second": "123"
        },
        "firstName" : "John",
        "lastName" : "Doe",
        "gender" : "m",
        "photo": "",
        "team": 1,
        "invitation": "__USER_INVITATION_CODE__"
      }
      """
    Then the response code should be 200
    And the response should contain "success"

  Scenario: User Registration - Empty Invitation Code
    When I send a "POST" request to "/app_acceptance.php/users/registrations" with body:
      """
      {
        "username": "s@test.com",
        "email": "s@test.com",
        "plainPassword": {
          "first": "123",
          "second": "123"
        },
        "firstName" : "John",
        "lastName" : "Doe",
        "gender" : "m",
        "photo": "",
        "team": 1,
        "invitation": ""
      }
      """
    Then the response code should be 200
    And the response should contain "error"

  Scenario: User Registration - Bad Invitation Code
    When I send a "POST" request to "/app_acceptance.php/users/registrations" with body:
      """
      {
        "username": "s@test.com",
        "email": "s@test.com",
        "plainPassword": {
          "first": "123",
          "second": "123"
        },
        "firstName" : "John",
        "lastName" : "Doe",
        "gender" : "m",
        "photo": "",
        "team": 1,
        "invitation": "111"
      }
      """
    Then the response code should be 200
    And the response should contain "error"