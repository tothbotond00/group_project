/*global $ tinyMCE */
/*eslint no-undef: "error"*/
/*eslint no-extend-native: ["error", { "exceptions": ["Number"] }]*/

import {getComments, postComment} from './repository';

export const init = (self, userid, groupid) => {
    $(function() {
        var objDiv = document.getElementById("comments");
        objDiv.scrollTop = objDiv.scrollHeight;
        $('#id_submit').click(async function(event) {
            event.preventDefault();
            Number.prototype.padLeft = function(base,chr){
                var  len = (String(base || 10).length - String(this).length)+1;
                return len > 0? new Array(len).join(chr || '0')+this : this;
            };
            $('#comments').append(commentWritten(self, tinyMCE.activeEditor.getContent(), formatDate(), 'own'));
            await postComment(userid, groupid, tinyMCE.activeEditor.getContent());
            tinyMCE.activeEditor.setContent('');
            var objDiv = document.getElementById("comments");
            objDiv.scrollTop = objDiv.scrollHeight;
        });
        window.setInterval(async function(){
            const comments = await getComments($('.bubbleWrapper').length,userid, groupid);
            comments.forEach(comment => {
                $('#comments').append(commentWritten(comment.userpix, comment.message, comment.time, comment.side));
                var objDiv = document.getElementById("comments");
                objDiv.scrollTop = objDiv.scrollHeight;
            });
        }, 10000);
    });
};

export const commentWritten = (self, comment, time, user) => {
    return '<div class="bubbleWrapper">\n' +
        `                    <div class="inlineContainer ${user}">\n` +
        `                        ${self}` +
        `                        <div class="otherBubble ${user}">\n` +
        `                            ${comment}\n` +
        '                        </div>\n' +
        '                    </div>\n' +
        `                    <span class="${user}">\n` +
        `                        ${time}\n` +
        '                    </span>\n' +
        '                </div>';
};

const formatDate = () => {
    Number.prototype.padLeft = function(base,chr){
        var  len = (String(base || 10).length - String(this).length)+1;
        return len > 0? new Array(len).join(chr || '0')+this : this;
    };
    const now = new Date;
    return [now.getFullYear().padLeft(),
            now.getMonth()+1,
            now.getDate().padLeft()].join('.') +' ' +
            [now.getHours().padLeft(),
            now.getMinutes().padLeft()].join(':');
};
