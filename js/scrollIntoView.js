function scrollIntoView() {
    let messages = []
    messages = document.querySelectorAll('.reply-message-media, .reply-message');


    messages.forEach((message, index) => {

        message.addEventListener('click', function () {
            const parentMessageIdReply = this.getAttribute('data-parent-id');

            if (!parentMessageIdReply) {
                return;
            }

            const replyMsgContent = document.querySelector(`.chat[data-message-id="${parentMessageIdReply}"]`);

            
            if (replyMsgContent) {
                replyMsgContent.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'  
                });
            
                replyMsgContent.style.transition = 'box-shadow 0.5s ease';
                replyMsgContent.style.boxShadow = '0 4px 8px rgba(143, 143, 143, 0.2)';  
            
                setTimeout(() => {
                    replyMsgContent.style.boxShadow = '0 0px 0px';  
                }, 2000);
            } else {
                console.log("Nenájdený prvok");
            }
            
        });
    });
}


function scrollDown (){
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    const usersMessages = document.querySelector('.chat-box');
    
    usersMessages.addEventListener('scroll', function() {
        const scrollPosition = usersMessages.scrollTop;
        const scrollHeight = usersMessages.scrollHeight;  
        const clientHeight = usersMessages.clientHeight;  

    
        if (scrollPosition < (scrollHeight - clientHeight - 200)) {
            scrollToTopBtn.style.display = "block";  
        } else {
            scrollToTopBtn.style.display = "none"; 
        }
    });
    
    scrollToTopBtn.addEventListener('click', function(event) {
    
        event.preventDefault();  
        event.stopPropagation();
    
        usersMessages.scrollTo({
            top: usersMessages.scrollHeight,  
            behavior: 'smooth'  
        });
    });
    

}
document.addEventListener('DOMContentLoaded', scrollIntoView);
