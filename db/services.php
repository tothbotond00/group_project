<?php

$functions = array(

    'mod_groupproject_post_comment' => array(
        'classname'     => 'mod_groupproject_external',
        'methodname'    => 'post_comment',
        'classpath'     => 'mod/groupproject/externallib.php',
        'description'   => 'Posts the comment int the given froup from user',
        'type'          => 'write',
        'capabilities'  => 'mod/groupproject:post_comment',
        'ajax' => true,
    ),

    'mod_groupproject_get_comments' => array(
        'classname'     => 'mod_groupproject_external',
        'methodname'    => 'get_comments',
        'classpath'     => 'mod/groupproject/externallib.php',
        'description'   => 'Gets the comments posted in the last x seconds',
        'type'          => 'read',
        'ajax' => true,
    )
);