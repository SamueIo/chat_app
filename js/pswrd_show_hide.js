const clickEye = document.querySelector(".fa-eye");
const pswrdFields = document.querySelectorAll(".form input[type='password']");

clickEye.onclick = () => {
    pswrdFields.forEach(pswrdField => {
        if (pswrdField.type === 'password') {
            pswrdField.type = 'text';  
            clickEye.classList.add('active');
        } else {
            pswrdField.type = 'password'; 
            clickEye.classList.remove('active'); 
        }
    });
};
