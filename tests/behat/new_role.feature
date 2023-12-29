@mod @mod_groupproject
Feature: Create new roles for a groupproject module
  In order to make the roles available to teachers as an admin I need to create roles
  To give capabilities to the role I can modify the role and its settings

  Scenario: Create a new role with empty form data.
    When I am on the "Manage groups" Activity page logged in as admin
    Then "Manage roles" "link" should exist
    And I press "Manage roles"
    And "Create a role" "button" should exist
    And I press "Create a role"
    And I press "Create"
    And I should see "- Required"

  Scenario: Create a new role with valid form data
    When I am on the "Manage groups" Activity page logged in as admin
    Then "Manage roles" "link" should exist
    And I press "Manage roles"
    And "Create a role" "button" should exist
    And I press "Create a role"
    And I set the field "Role name" to "Role name 1"
    And I set the field "Role description" to "Role desc 1"
    And I press "Create"
    And I should see "Role name 1" in the "Role name" "table_row"