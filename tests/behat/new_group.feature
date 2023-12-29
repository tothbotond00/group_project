@mod @mod_groupproject
Feature: Create new groups for a groupproject module
  In order to make the module available to students as a teacher I need to create groups
  To increase the group size I can modify the group and its settings

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

  Scenario: Create a new group for an empty activity with empty form data.
    Given the following "activity" exists:
      | activity                            | groupproject      |
      | course                              | C1                |
      | name                                | Groupproject name |
    When I am on the "Manage groups" Activity page logged in as teacher1
    Then "Create a group" "button" should exist
    And I press "Create a group"
    And I press "Create"
    And I should see "- Required"

  Scenario: Create a new group for an empty activity with invalid size for the group.
    Given the following "activity" exists:
      | activity                            | groupproject      |
      | course                              | C1                |
      | name                                | Groupproject name |
    When I am on the "Manage groups" Activity page logged in as teacher1
    Then "Create a group" "button" should exist
    And I press "Create a group"
    And I set the field "Group name" to "Group name 1"
    And I set the field "Group size" to "-2"
    And I press "Create"
    And I should see "Invalid group size"

  Scenario: Create a new group for an empty activity with valid form data.
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
