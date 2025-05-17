const form = document.querySelector(".signup form");
const continueBtn = form.querySelector(".button input");
const errorTxt = form.querySelector(".error-txt");

let nameDetailsLog = document.getElementById('name-details');
let linkAA = document.querySelectorAll('.link');

let url=''
function formChecker() {
    if (nameDetailsLog.classList.contains('hidden')) {
        return "./php/signup.php";
    } else {
        return "./php/login.php"; 
        
    }
}

url=formChecker()

form.addEventListener('submit', (e) => {
    e.preventDefault(); 
});

linkAA.forEach(function(link) {
    link.addEventListener('click', function() {
        url = formChecker();
    });
});
continueBtn.onclick = () => {
    
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);

    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            let data = xhr.response.trim();
             console.log(data);
            if (data === 'success') {
                location.href = "user.php";
            } else {
                errorTxt.textContent = data;
                errorTxt.classList.add('show');
            }
        }
    };

    let formData = new FormData(form);
    xhr.send(formData);
};
