document.on('load',(event) => {
    var objDiv = document.getElementById("comments");
    objDiv.scrollTop = objDiv.scrollHeight;
})