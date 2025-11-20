document.addEventListener('DOMContentLoaded', function() {
    var alerts= document.querySelectorAll('.alert');
    if(alerts.length > 0) {
        //Redirection après 3 secondes
        setTimeout(function() {
            window.location.href = "/";
        }, 3000);
    }
});