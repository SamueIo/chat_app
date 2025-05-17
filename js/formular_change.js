let fname = document.querySelector(".fname");
let lname = document.querySelector(".lname");
let passwordControl = document.querySelector(".passwordControl");

let nameDetails = document.getElementById('name-details');
let secondPassword = document.getElementById('second-password');
let registration = document.getElementById('registration');
let forgotPassword = document.querySelector('.forgotPassword');
let signin = document.getElementById('signin');
let linkA = document.querySelector('.link');





function updateFormState() {
    if (nameDetails.classList.contains('hidden')) {
        
        fname.setAttribute('required', true);
        lname.setAttribute('required', true);
        passwordControl.setAttribute('required', true);

        fname.removeAttribute('disabled');
        lname.removeAttribute('disabled');
        passwordControl.removeAttribute('disabled');
        
        
    } else {

        fname.removeAttribute('required');
        lname.removeAttribute('required');
        passwordControl.removeAttribute('required');

        fname.setAttribute('disabled', true);
        lname.setAttribute('disabled', true);
        passwordControl.setAttribute('disabled', true);

        

       
    }
}
updateFormState()

document.querySelectorAll('#toggleLink, #toggleLinkReg').forEach(function(link) {
    link.addEventListener('click', function(event) {
        event.preventDefault();

        

        nameDetails.classList.toggle('hidden');
        secondPassword.classList.toggle('hidden');
        registration.classList.toggle('visible');
        forgotPassword.classList.toggle('hidden')
        signin.classList.toggle('hidden');

        updateFormState()
    });
});
