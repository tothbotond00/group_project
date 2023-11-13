<?php

namespace mod_groupproject\local\entities;

use completion_info;
use core\context;
use mod_groupproject\local\factories\entity_factory;
use mod_groupproject\local\loaders\entity_loader;

class groupproject extends entity {

    /** @var string $TABLE DB table of class */
    public static $TABLE = 'groupproject';

    /** @var int $course ID of the course  */
    protected $course;
    /** @var string $name Name of groupproject */
    protected $name;
    /** @var string $intro Intro of the groupproject */
    protected $intro;
    /** @var int $intro Intro of the groupproject */
    protected $introformat;
    /** @var int $duedate Duedate for the groupproject */
    protected $duedate;
    /** @var int $grade Maxgrade for the groupproject */
    protected $grade;
    /** @var int $timecreated Group creation unix timestamp */
    protected $timecreated;
    /** @var int $timemodified Group creation unix timestamp */
    protected $timemodified;
    /** @var array $groups The Groups associated with the project */
    protected $groups = array();

    /**
     * @param int $id
     * @param int $course
     * @param string $name
     * @param string $intro
     * @param int $introformat
     * @param int $timecreated
     * @param int $timemodified
     */
    public function __construct(
        int $id,
        int $course,
        string $name,
        string $intro,
        int $introformat,
        int $duedate,
        int $grade,
        int $timecreated,
        int $timemodified
    ) {
        $this->id = $id;
        $this->course = $course;
        $this->name = $name;
        $this->intro = $intro;
        $this->introformat = $introformat;
        $this->duedate = $duedate;
        $this->grade = $grade;
        $this->timecreated = $timecreated;
        $this->timemodified = $timemodified;
        $this->loadGroups();
    }

    /**
     * @return int
     */
    public function getCourse(): int
    {
        return $this->course;
    }

    /**
     * @param int $course
     */
    public function setCourse(int $course): void
    {
        $this->course = $course;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getIntro(): string
    {
        return $this->intro;
    }

    /**
     * @param string $intro
     */
    public function setIntro(string $intro): void
    {
        $this->intro = $intro;
    }

    /**
     * @return int
     */
    public function getIntroformat(): int
    {
        return $this->introformat;
    }

    /**
     * @param int $introformat
     */
    public function setIntroformat(int $introformat): void
    {
        $this->introformat = $introformat;
    }

    /**
     * @return int
     */
    public function getDuedate(): int
    {
        return $this->duedate;
    }

    /**
     * @param int $duedate
     */
    public function setDuedate(int $duedate): void
    {
        $this->duedate = $duedate;
    }

    /**
     * @return int
     */
    public function getGrade(): int
    {
        return $this->grade;
    }

    /**
     * @param int $grade
     */
    public function setGrade(int $grade): void
    {
        $this->grade = $grade;
    }

    /**
     * @return int
     */
    public function getTimecreated(): int
    {
        return $this->timecreated;
    }

    /**
     * @param int $timecreated
     */
    public function setTimecreated(int $timecreated): void
    {
        $this->timecreated = $timecreated;
    }

    /**
     * @return int
     */
    public function getTimemodified(): int
    {
        return $this->timemodified;
    }

    /**
     * @param int $timemodified
     */
    public function setTimemodified(int $timemodified): void
    {
        $this->timemodified = $timemodified;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     */
    public function setGroups(array $groups): void
    {
        $this->groups = $groups;
    }

    public function getCourseInstance() :?\stdClass
    {
        global $DB;
        return $DB->get_record('course', array('id' => $this->course));
    }

    public function getCourseModule() : \stdClass
    {
        global $DB;
        $courseid = $this->course;
        $moduleid = $DB->get_field('modules', 'id', array('name' => 'groupproject'));
        $instanceid = $this->id;
        return $DB->get_record('course_modules', array('course' => $courseid, 'module' => $moduleid,'instance' => $instanceid));
    }

    public function getInstance() : \stdClass
    {
        return (object)(array)$this;
    }

    public function getContext() : context
    {
        $coursemoudleid = $this->getCourseModule()->id;
        return \context_module::instance($coursemoudleid);
    }

    private function loadGroups()
    {
        global $DB;

        $this->groups = array();
        $groups = $DB->get_records(group::$TABLE, array('groupprojectid' => $this->id));

        foreach ($groups as $group){
            $this->groups[] = entity_factory::create_group_from_stdclass($group);
        }
    }

    public function resetUserdata(){
        $componentstr = get_string('modulenameplural', 'projecttask');
        foreach ($this->groups as $group){
            $group->delete();
        }
        return array(
            array(
                'component' => $componentstr,
                'item' => get_string('useroverridesdeleted', 'projecttask'),
                'error' => false)
        );
    }

    public function delete()
    {
        foreach ($this->groups as $group) {
            $group->delete();
        }

        parent::delete();
    }

    public function setModuleViewed() {
        $completion = new completion_info($this->getCourseInstance());
        $completion->set_module_viewed($this->getCourseModule());

        // Trigger the course module viewed event.
        $instance = $this->getInstance();
        $params = [
            'objectid' => $this->id,
            'context' => $this->getContext()
        ];

        $event = \mod_groupproject\event\course_module_viewed::create($params);

        $event->add_record_snapshot('projecttask', $instance);
        $event->trigger();
    }

    public function userHasGroup($userid = 0): group|bool
    {
        global $USER, $DB;
        $user = new \stdClass();
        if($userid == 0) $user = $USER;
        else $user = $DB->get_record('user', ['id' => $userid]);
        foreach ($this->groups as $group){
            if(in_array($user->id,$group->getUserIds())) return $group;
        }
        return false;
    }

    public function getPossibleUsers() {
        $users = [];
        foreach($this->getGroups() as $group){
            if(!empty($group_users = $group->getUsers())){
                $users = array_merge($users + $group_users);
            }
        }
        $enrolled_users = get_enrolled_users($this->getContext()->get_course_context());
        $possible_users = [];
        foreach ($enrolled_users as $enrolled_user){
            if(!in_array($enrolled_user->id, $users)){
                $possible_users[$enrolled_user->id] = $enrolled_user;
            }
        }
        return $possible_users;
    }

}