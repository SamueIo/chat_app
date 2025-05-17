
let userClass = document.querySelector(".logged-user");
let logged_userId = userClass.getAttribute("logged-user-id");
userList= document.querySelector('.users-list')
let firstNavTxt = document.getElementById("firtNavTxt")



let chatInterval;
findContact()
updateLastActive(logged_userId)


function startLoadingMessages(chatId) {

    if (chatInterval) {
        return;
    }

    loadLatestMessages(chatId, logged_userId);
    let chatContainer = document.getElementById('chat-box');
    chatContainer.scrollTop = chatContainer.scrollHeight;

    chatInterval = setInterval(function() {
        loadLatestMessages(chatId, logged_userId); 
        observeMessages()
    }, 2000);
}


function stopLoadingMessages() {
    if (chatInterval) {
        clearInterval(chatInterval);
        chatInterval = null;  

    }
    
}
function observeMessages() {
    const interval = setInterval(() => {
        let loadedMessages = document.querySelectorAll('.chat');

        if (loadedMessages.length > 0) {
            loadedMessages.forEach(message => {
                if (message.innerText.trim() !== '') {
                    observer.observe(message);
                }
            });

            clearInterval(interval);
        }
    }, 500); 
}
let userId =null;
userList.addEventListener('click', function(e) {
    let userElement = e.target.closest('.user');
    
    if (userElement) {
        stopLoadingMessages();

        userId = userElement.getAttribute('data-user-id');
    
        openChatIdStr = [Number(logged_userId), Number(userId)].sort((a, b) => a - b).join('');
        openChatId = Number(openChatIdStr);

        if(parentalMessageDiv && parentalMessageDiv.innerHTML != '' ){
            parentalMessageDiv.innerHTML = ''; 
        }

        getUserInfo(userId);
        
        if (!openChatId) {
            console.error("openChatId je neplatné!");
            return;
        }
        
        createChat(logged_userId, userId);
        if(openChat(logged_userId, userId, openChatId)){
            const chatContainer = document.getElementById('chat-box');
            chatContainer.scrollTop = chatContainer.scrollHeight;
            
        }
        goBackButton()
        
        
        
        /****************************************************** */
        setTimeout(() => {
            startLoadingMessages(openChatId);
        }, 500);
    
        /****************************************************** */

        
        
        
        
        observeMessages();
        
        setTimeout(() => {
            photoView ()

        }, 500);
        setTimeout(() => {
            //pre zobrazenie fotiek 
            scrollIntoView()
            //pre scrolovanie na najnovsie spravy
            scrollDown ()
        }, 1000);
        //spustenie funkciena upravu statusu kvoli malym obrazovkam 
        setInterval(changeStatusView, 10000);

    } 
    
    const chatContainer = document.getElementById('chat-box');
    chatContainer.scrollTop = chatContainer.scrollHeight;
});
let typingAreaInput = document.querySelector(".typing-area input");
let typingAreaButton = document.querySelector(".typing-area button");
let parentalMessageDiv = document.querySelector(".replying-message");
let replyMessageId = '';
let parentalMessage = '';

document.querySelector(".users-messages").addEventListener('click', function(event) {

    if (event.target && (event.target.classList.contains('reply-btn') || event.target.closest('.reply-btn'))) {
        typingAreaInput.focus();
        const chatElement = event.target.closest(".chat");
        const RmessageId = chatElement.getAttribute("data-message-id");
        replyMessageId = RmessageId;

        let messageContentFromId = document.querySelector(`[data-message-id='${replyMessageId}']`);

        if (messageContentFromId) {
            let parentalMessage = "";

            let details = messageContentFromId.querySelector(".details");
            if (details) {
                parentalMessage = details.querySelector("p").textContent;
            } else {
                let chatMedia = messageContentFromId.querySelector(".chat-media");
                if (chatMedia) {
                    parentalMessage = chatMedia.alt ||  'Media';
                }
            }

            if (parentalMessageDiv) {
                parentalMessageDiv.innerHTML = "Odpovedať na: " + parentalMessage;

                let deleteButton = document.createElement('button');
                deleteButton.type = 'button';
                deleteButton.id = 'delete-message';
                deleteButton.innerHTML = '<i class="fa-solid fa-trash"></i>';

                deleteButton.addEventListener('click', function() {
                    parentalMessageDiv.innerHTML = '';  
                    replyMessageId = '';  
                });

                parentalMessageDiv.appendChild(deleteButton); 
            }
        }
    }
});
document.getElementById('file-input').addEventListener('change', function(event) {
    var fileCount = event.target.files.length;  
    document.getElementById('file-count').textContent = fileCount;  
});
let firstMessageId = 0;

typingAreaButton.addEventListener("click", (e) => {
    typingAreaInput.focus();
    document.getElementById('file-count').textContent =''
    e.preventDefault();
    if (!openChatId) {
        console.error("openChatId je neplatné! Zastavené volanie.");
        return;
    }




//pre zobrazenie prvej spravy pretoze ak id==0 spravy sa nenačitaju
    let noMessages = document.querySelector(".no-message-chat");
    let noMessagesDoubleCheck = false
    if (noMessages) {
        firstMessageId = 0.5;
        noMessages.remove();
        noMessagesDoubleCheck = true
        
    } else if (!noMessages && noMessagesDoubleCheck) {
        setTimeout(() => {
            firstMessageId = 0;
        }, 1000);
    }
 
    sendMessage(openChatId, logged_userId, replyMessageId);
    parentalMessageDiv.innerHTML=''
    replyMessageId=''
    setTimeout(() => {
        photoView ()
        // pre reply sprav
        // scrollIntoView()
 

    }, 500);
    
        
    
});



