@mod @mod_groupproject
Feature: Manage roles for a groupproject module
  In order to make the module work for teachers as an admin I need to manage roles
  The manage roles table will help me manage to data associated with the roles

  Scenario: Create a new role with empty form data.
    When I am on the "Manage groups" Activity page logged in as admin
    Then "Manage roles" "link" should exist
    And I press "Manage roles"
    And "Create a role" "button" should exist
    And I should see "Add role"

  Scenario: Create a new role with empty form data.
    When I am on the "Manage groups" Activity page logged in as admin
    Then "Manage roles" "link" should exist
    And I press "Manage roles"
    And "Role name" "table_row" should not exist

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

  Scenario: Create a new role with valid form data
    When I am on the "Manage groups" Activity page logged in as admin
    Then "Manage roles" "link" should exist
    And I press "Manage roles"
    And "Create a role" "button" should exist
    And I press "Create a role"
    And I set the field "Role name" to "Role name 1"
    And I set the field "Role description" to "Role desc 1"
    And I press "Create"
    And "Modify" "icon" should exist in the "Role name 1" "table_row"
    And "Delete" "icon" should exist in the "Role name 1" "table_row"