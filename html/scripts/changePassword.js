$(function() {
    //Change the appearance of the nav bar to indicate current tab
    $('#schedule-tab').toggleClass("active")
    $('#schedule-tab').attr("aria-selected", "true")
})

var check = function() {
            if (document.getElementById('newPass').value ==
                document.getElementById('confirmPass').value) {
                document.getElementById('message').style.color = 'green';
                document.getElementById('message').innerHTML = 'matching';
                document.getElementById('submit').disabled = false;
            } else {
                document.getElementById('message').style.color = 'red';
                document.getElementById('message').innerHTML = 'not matching';
                document.getElementById('submit').disabled = true;
            }
}