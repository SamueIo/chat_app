
let leftSide = document.querySelector(".left-side");
let rightSide = document.querySelector(".right-side");
let userListSec = document.querySelector('.users-list');

function goBackButton(){
    history.pushState(null, '', location.href);

    window.addEventListener('popstate', function (event) {
        if (window.innerWidth <= 700) {
            if (leftSide.classList.contains("hidden")) {
                rightSide.classList.add("hidden");
                leftSide.classList.remove("hidden")
            }
        }
    });
}
function handleWindowResize() {
    
    if (window.innerWidth <= 700) {
        
        if (!leftSide.classList.contains("hidden")) {
            rightSide.classList.add("hidden");
        }
    } else {
        if (leftSide.classList.contains("hidden")) {
            rightSide.classList.remove("hidden");
        }
    }
    if (window.innerWidth > 700) {
        

        if (rightSide.classList.contains("hidden")) {
            rightSide.classList.remove("hidden");
            
        }
        if(leftSide.classList.contains("hidden")){
            leftSide.classList.remove("hidden")
        }
    }
}

document.addEventListener("DOMContentLoaded", handleWindowResize);


rightSide.addEventListener("click", (event) => {
    if (event.target && event.target.id === "go-back-arrow") {
        if (leftSide.classList.contains("hidden")) {
            leftSide.classList.remove("hidden");
            rightSide.classList.add("hidden");
        } else {
            rightSide.classList.remove("hidden");
            leftSide.classList.add("hidden");
        }
    }
});


document.addEventListener("DOMContentLoaded", function() {
    userListSec.addEventListener("click", () => {
        leftSide.classList.add("hidden");
        rightSide.classList.remove("hidden");
    });
});
function scrollToBottom() {
    const chatBox = document.querySelector('.chat-box');
    chatBox.scrollTop = chatBox.scrollHeight;
}


scrollToBottom();

window.addEventListener("resize", () => {
    setTimeout(() => {
        handleWindowResize();
    }, 100);
});