/*global $ */
/*eslint no-undef: "error"*/

export const init = (users, roles, size, contextid, groupid, groupusers) => {
    $(function(){
        //Buttons
        const user_view = $('#user_view');
        const add_line = '<button id="add_line"' +
            ' class="btn btn-outline-secondary btn-sm text-nowrap" style="margin-bottom: 20px"> Új Felhasználó</button>';
        const send_data = '<button id="send_data"' +
            ' class="btn btn-success btn-sm text-nowrap" style="margin-bottom: 20px"> Felhasználók hozzáadása</button>';
        user_view.append(add_line);
        user_view.append('<br>');
        user_view.append(send_data);

        //Form
        initilaiseForm(users, roles, groupusers);
        if($('.form_element').length >= size){
            $("#add_line").prop('disabled', true);
        }
        else if ($('.form_element').length === 0) {
            addFormElement(users, roles);
        }

        //Button Events
        $("#add_line").click(function() {
            addFormElement(users,roles);
            if($('.form_element').length >= size){
                $("#add_line").prop('disabled', true);
            }
        });
        $("#send_data").click(function(){
            sendData(contextid, groupid);
        });

    });
};

export const initilaiseForm = (users,roles,groupusers) => {
    let i = 0;
    // eslint-disable-next-line no-unused-vars
    for (const [key, value] of Object.entries(groupusers)) {
        const generated_div = generateFormElement(users, roles);
        $(generated_div).insertBefore($("#add_line"));
        $(`#users:eq(${i})`).val(value.userid).change();
        $(`#roles:eq(${i})`).val(value.roleid).change();
        i++;
    }
    $('.delete_row').click(function(event) {
        event.preventDefault();
        $(this).closest('div').remove();
        $('#add_line').prop('disabled', false);
    });
};

export const addFormElement = (users, roles) => {
    $(generateFormElement(users, roles)).insertBefore($("#add_line"));
    $('.delete_row').click(function(event) {
        event.preventDefault();
        $(this).closest('div').remove();
        $('#add_line').prop('disabled', false);
    });
};

export const generateFormElement = (users, roles) => {
    let user_options = '';
    let role_options = '';
    // eslint-disable-next-line no-unused-vars
    for (const [key, value] of Object.entries(users)) {
        let name = value.lastname + " " + value.firstname;
        let id = value.id;
        user_options += `<option value=${id}>${name}</option>`;
    }
    // eslint-disable-next-line no-unused-vars
    for (const [key, value] of Object.entries(roles)) {
        let name =  value.name;
        let id = value.id;
        role_options += `<option value=${id}>${name}</option>`;
    }
    return '<div style="margin-bottom: 20px" class="form_element">' +
                '<label for="users">Felhasználó</label>\n' +
                '\n' +
                '<select name="users" id="users" class="custom-select" style="margin-right: 20px">\n' +
                    user_options +
                '</select>' +
                '<label for="roles">Szerepkör</label>\n' +
                '\n' +
                '<select name="roles" id="roles" class="custom-select" style="margin-right: 20px">\n' +
                    role_options +
                '</select>' +
                '<a class="action-icon delete_row">' +
                    '<i class="icon fa fa-trash fa-fw " title="Delete" role="img" aria-label="Delete"></i>' +
                '</a>' +
                '<br> ' +
            '</div>';
};

export const sendData = (contextid, groupid) => {
    const formData = $("#user_view div");
    let sendArray = [];
    $.each(formData, function(i, v) {
        const div = $(v);
        const userid = div.find( "#users").val();
        const roleid = div.find( "#roles").val();
        sendArray.push({"userid" : userid, "roleid" : roleid});
    });
    const json = JSON.stringify(sendArray);
    $(`<form action="/mod/groupproject/user.php?id=${contextid}&groupid=${groupid}" method="post">
        <input type="hidden" name="jQueryData" value='${json}'>
        </form>`)
        .appendTo('body').submit();
};