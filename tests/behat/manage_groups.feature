@mod @mod_groupproject
Feature: Manage groups for a groupproject module
  In order to make the module work for students as a teacher I need to manage groups
  The manage group table will help me manage to data associated with the groups

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Create group button can be found and working
    Given the following "activity" exists:
      | activity                            | groupproject      |
      | course                              | C1                |
      | name                                | Groupproject name |
    When I am on the "Manage groups" Activity page logged in as teacher1
    Then "Create a group" "button" should exist
    And I press "Create a group"
    And I should see "Create group"

  Scenario: Manage roles link is available and working
    Given the following "activity" exists:
      | activity                            | groupproject      |
      | course                              | C1                |
      | name                                | Groupproject name |
    When I am on the "Manage groups" Activity page logged in as teacher1
    Then "Manage roles" "link" should exist
    And I press "Manage roles"
    And I should see "Manage roles"

  Scenario: Empty activity shouldn't contain table
    Given the following "activity" exists:
      | activity                            | groupproject      |
      | course                              | C1                |
      | name                                | Groupproject name |
    When I am on the "Manage groups" Activity page logged in as teacher1
    Then "Group name" "table_row" should not exist

  Scenario: Created group makes the table appear
    Given the following "activity" exists:
      | activity                            | groupproject      |
      | course                              | C1                |
      | name                                | Groupproject name |
    When I am on the "Manage groups" Activity page logged in as teacher1
    Then "Create a group" "button" should exist
    And I press "Create a group"
    And I set the field "Group name" to "Group name 1"
    And I set the field "Group size" to "3"
    And I press "Create"
    And I should see "Group name 1" in the "Group name" "table_row"

  Scenario: Created group makes the table have icons that can be pressed
    Given the following "activity" exists:
      | activity                            | groupproject      |
      | course                              | C1                |
      | name                                | Groupproject name |
    When I am on the "Manage groups" Activity page logged in as teacher1
    Then "Create a group" "button" should exist
    And I press "Create a group"
    And I set the field "Group name" to "Group name 1"
    And I set the field "Group size" to "3"
    And I press "Create"
    And "Add students" "icon" should exist in the "Group name 1" "table_row"
    And "Modify" "icon" should exist in the "Group name 1" "table_row"
    And "Grade" "icon" should exist in the "Group name 1" "table_row"
    And "Delete" "icon" should exist in the "Group name 1" "table_row"