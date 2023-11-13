import {call as fetchMany} from 'core/ajax';

export const postComment = (userid, groupid, content) => {
    const args = {
        userid,
        groupid,
        content,
    };

    return fetchMany([{methodname: 'mod_groupproject_post_comment', args}])[0];
};

export const getComments = (count, userid, groupid) => {
    const args = {
        count,
        userid,
        groupid,
    };

    return fetchMany([{methodname: 'mod_groupproject_get_comments', args}])[0];
};
