$(document).ready(function(){
    $('form.one-time-submit').submit(function(e) {
        $(this).unbind('submit');
        $(this).submit(function () {
            return false;
        });
        return true;
    });
});
