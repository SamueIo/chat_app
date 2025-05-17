
function createChat(logged_userId, userId) {
    let xhr = new XMLHttpRequest();
    xhr.open('POST', "./php/createChat.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                let response = JSON.parse(xhr.responseText);

                if (response.create_chat && response.create_chat.status === 'success') {
                    let chatId = response.create_chat.chat_id;
                
                    try {
                        
                        if (typeof chatId === 'string') {
                            chatId = JSON.parse(chatId);
                        }
                
                    
                    } catch (e) {
                        console.error("Chyba pri parsovaní chat_id:", e);
                    }
                
                
                } else {
                    console.log("Chyba pri vytváraní chatu: " + response.create_chat.message || "Neznáma chyba");
                }
            } catch (e) {
                console.log("Chyba pri spracovaní odpovede: " + e.message);
                console.error("Chyba pri spracovaní JSON odpovede:", e);
                console.log("Odpoveď zo servera neobsahuje validný JSON:", xhr.responseText);
            }
        } else {
            console.log("Chyba požiadavky: " + xhr.status);
        }
    };

    xhr.send("user1_id=" + logged_userId + "&user2_id=" + userId);
}
function formatDate(dateStr) {
    const date = new Date(dateStr);  

    const day = date.getDate();  
    const month = date.getMonth() + 1;  // Mesiac je 0-indexovaný, preto + 1
    const year = date.getFullYear();  

    return `${day}.${month}.${year}`;
}



function openChat(logged_userId, userId) {
    let xhr = new XMLHttpRequest();
    xhr.open('POST', "./php/createChat.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    let user1_id = Number(logged_userId);
    let user2_id = Number(userId);

    let chatContainer = document.getElementById('chat-box');
    let isScrolledToBottom = (chatContainer.scrollHeight - chatContainer.scrollTop === chatContainer.clientHeight);

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                let response = JSON.parse(xhr.responseText);  
                if (response.messages) {

                    if (typeof response.messages === "string") {
                        try {
                            response.messages = JSON.parse(response.messages);
                        } catch (e) {
                            console.log("Chyba pri parsovaní messages:", e);
                        }
                    }

                    let chatBoxes = document.querySelector('.users-messages');
                    let chatBoxDate = document.querySelector('.users-messages');
                    chatBoxes.innerHTML = '';

                    let wroteDate = '';  
                    response.messages.forEach(message => {
                        let replyMessage=''
                
                        let messageDiv = document.createElement('div');
                        let messageDivDate = document.createElement('div');
                        messageDivDate.classList.add("message-date")
                        let contentHtml = ''
                        let contentHtmlDate = ''
                        contentHtmlButton= ''

                        let timeMessage = message.created_at;
                        dayDate = formatDate(timeMessage)
                        let date = new Date(timeMessage);

                        let hours = (date.getHours() + 9) % 24; 
                        hours = hours.toString().padStart(2, '0');  

                        let minutes = date.getMinutes().toString().padStart(2, '0');
                        let formattedTime = `${hours}:${minutes}`;

                        const messageDate = message.created_at.split(' ')[0];  
                        const [year, month, day] = messageDate.split('-');
                        const formattedMessageDate = `${parseInt(day)}.${parseInt(month)}.${year}`;

                        


                        if (wroteDate === '' || wroteDate !== formattedMessageDate) {
                            if (wroteDate === '') {
                              wroteDate = formattedMessageDate;  
                              contentHtmlDate += `
                                  <p> ${formattedMessageDate}</p>
                              `;
                            } else {
                            const lastDate = new Date(wroteDate.split('.').reverse().join('-'));
                            const currentDate = new Date(formattedMessageDate.split('.').reverse().join('-'));

                                if (!isNaN(lastDate) && !isNaN(currentDate)) {
                                  const diffTime = Math.abs(currentDate - lastDate);
                                  const diffDays = diffTime / (1000 * 60 * 60 * 24);
                                
                                  if (diffDays >= 1) {
                                    wroteDate = formattedMessageDate;
                                    contentHtmlDate += `
                                        <p> ${formattedMessageDate}</p>
                                    `;

                                  }
                                } else {
                                }
                              }
                            } 


                            
                            if(message.message != '' && message.parent_message_id !=0){
                                let reply = response.messages.find(msg => msg.id === message.parent_message_id);
                                if (reply) {
                                    replyMessage = reply.message;

                                    if (reply.media && reply.media.length > 0) {
                                        let replyImage = reply.media.find(file => file.file_type.startsWith('image'));
                                        if (replyImage) {
                                            contentHtml += `
                                                <div class="reply-message-media" data-parent-id="${message.parent_message_id}">
                                                    <p> ${replyMessage}</p>
                                                    <img src="${replyImage.file_url}" alt="Obrázok" class="reply-image">
                                                </div>
                                                <div class="details">
                                                    <button class="reply-btn"><i class="fa-solid fa-reply"></i></button>
                                                    <span class='message-info-cantent'> <p class ='message-content-para'>${message.message}</p> <p class='message-time'> ${formattedTime}</p></span> 
                                                 </div>
                                            `;
                                        }else if (reply.media.find(file => file.file_type.startsWith('video'))) {
                                            contentHtml += `
                                                <div class="reply-message-media" data-parent-id="${message.parent_message_id}">
                                                    <p> ${replyMessage}</p>
                                                    <source src="${replyImage.file_url}" alt="Video" class="reply-video">
                                                </div>
                                                <div class="details">
                                                    <button class="reply-btn"><i class="fa-solid fa-reply"></i></button>
                                                    <span class='message-info-cantent'> <p class ='message-content-para'>${message.message}</p> <p class='message-time'> ${formattedTime}</p></span> 
                                                 </div>
                                            `;
                                        }else{
                                            contentHtml += `
                                                <div class="reply-message-media" data-parent-id="${message.parent_message_id}">
                                                    <p> ${replyMessage}</p>
                                                    <source src="${replyImage.file_url}" alt="ostatné">
                                                </div>
                                                <div class="details">
                                                    <button class="reply-btn"><i class="fa-solid fa-reply"></i></button>
                                                    <span class='message-info-cantent'> <p class ='message-content-para'>${message.message}</p> <p class='message-time'> ${formattedTime}</p></span> 
                                                 </div>
                                            `;
                                        }
                                    }else{
                                        contentHtml +=`
                                        <div class="reply-message" data-parent-id="${message.parent_message_id}">
                                            <p> ${replyMessage}</p>
                                        </div>
                                        <div class="details">
                                            <button class="reply-btn"><i class="fa-solid fa-reply"></i></button>
                                            <span class='message-info-cantent'> <p class ='message-content-para'>${message.message}</p> <p class='message-time'> ${formattedTime}</p></span> 
                                        </div>
                                    `;
                                    }
                                }

                            }else if (message.message != '' && message.parent_message_id ==0){
                                    contentHtml += `
                                    <div class="details">
                                        <button class="reply-btn"><i class="fa-solid fa-reply"></i></button>
                                        <span class='message-info-cantent'> <p class ='message-content-para'>${message.message}</p> <p class='message-time'> ${formattedTime}</p></span> 
                                    </div>
                                `;

                            }
                        


                        

                            if (message.media && message.media.length > 0) {
                                contentHtml += `<div class="message-media-button-container">`;
                            
                                contentHtml += `
                                <button class="reply-btn"><i class="fa-solid fa-reply"></i></button>
                                `;
                                contentHtml += `<div class="message-media-container">`;
                                
                                message.media.forEach(file => {
                                    if (file.file_type.startsWith('image')) {
                                        contentHtml += `
                                        <div class="chat-media">
                                            <img src="${file.file_url}" alt="Obrázok" class="chat-media-img">
                                        </div>`;
                                    } else if (file.file_type.startsWith('video')) {
                                        contentHtml += `
                                        <div class="chat-media">
                                            <video controls class="chat-media">
                                                <source src="${file.file_url}" type="${file.file_type}">
                                            </video>
                                        </div>`;
                                    } else if (file.file_type.startsWith('audio')) {
                                        contentHtml += `
                                        <div class="chat-media">
                                            <audio   class="player chat-media">
                                                <source src="${file.file_url}" type="${file.file_type}">
                                                 
                                            </audio>
                                        </div>`;
                                    }
                                    
                                });
                            
                                contentHtml += `</div>`;
                            
                                contentHtml += `</div><p class='message-time'> ${formattedTime}</p>`;
                            }
                            
                            if (message.user_id === user1_id) {
                                messageDiv.classList.add('chat');
                                messageDiv.classList.add('outgoing');
                            } else {
                                messageDiv.classList.add('chat');
                                messageDiv.classList.add('incoming'); 
                            }
                            
                            if(contentHtmlDate !=''){
                                messageDivDate.classList.add("message-date")
                                messageDivDate.innerHTML=contentHtmlDate;
                                chatBoxDate.appendChild(messageDivDate)
                            }

                            
                            

                            messageDiv.dataset.messageId = message.id;
                            messageDiv.innerHTML = contentHtmlButton + contentHtml;
                            chatBoxes.appendChild(messageDiv);

                            

                    });
                    let messageDiv = document.createElement('div');
                        let contentHtml = ''
                        
                    if (response.messages.length === 0) {
                        contentHtml += `
                            <div class="no-message-chat">
                                <p>Žiadné správy</p>
                            </div>
                        `;
                        messageDiv.innerHTML = contentHtml;
                        chatBoxes.appendChild(messageDiv);
                    }
                    
                    if (isScrolledToBottom) {
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }

                   

                } else {
                    console.log("Chyba: 'messages' neexistuje v odpovedi.");
                }
            } catch (e) {
                console.log("Chyba pri spracovaní odpovede: no files " + e.message);
            }
        } else {
            console.log("Chyba požiadavky: " + xhr.status);
        }
    };
    

    xhr.send("user1_id=" + user1_id + "&user2_id=" + user2_id);
    findContact()
    setTimeout(() => {
        playerVersion()

    }, 1000);

}
function playerVersion() {
    const players = Plyr.setup('.player', {
        controls: ['play', 'progress'],
        tooltips: { controls: true },
    });
}


function findContact(){
    setTimeout(() => {
        
        if (userList && userList.childElementCount > 0) {
            firstNavTxt.style.opacity = 0;
    
            firstNavTxt.addEventListener('transitionend', () => {
                firstNavTxt.style.display = 'none';
            });
        }
    }, 1500);
}



let messageIdForMedia =''
let containerMedia = ''
function appendMessage(inputMessage, user_id, logged_userId, lastMessageId, fileUrl, fileType, parentMessageIdReply) {
    let lastMessageCheck= ''
    if (inputMessage === undefined && fileUrl === undefined) {
        console.error("Správa je undefined a chýba aj súbor.");
        return;
    }

    if (fileUrl !== undefined) {
        photoView(fileUrl);
    }

    
    const messageContainer = document.querySelector(".users-messages");
    const messageDiv = document.createElement('div');
    let contentHtml = '';
    let parentContentHtml = '';
    let wholeContainer = '';
    const buttonReply = `<button class="reply-btn"><i class="fa-solid fa-reply"></i></button>`;

    function createMediaHtml() {
        let messageIdForMedia = lastMessageId || '';  

        lastMessageCheck = document.querySelector(`[data-message-id='${messageIdForMedia}']`);


        wholeContainer += `<div class="message-media-button-container">`;

        if (fileType && lastMessageCheck) {

            let mediaHtml = '';

            if (fileType.startsWith('image')) {
                mediaHtml = `<div class="chat-media"><img src="${fileUrl}" alt="Obrázok" class="chat-media"></div>`;
            } else if (fileType.startsWith('video')) {
                mediaHtml = `<div class="chat-media"><video controls class='chat-media'><source src="${fileUrl}" type="video/mp4"></video></div>`;
            } else if (fileType.startsWith('audio')) {
                mediaHtml = `<div class="chat-media"><audio  class="player chat-media'><source src="${fileUrl}" type="audio/mpeg"></audio></div>`;
            }

            if (mediaHtml && lastMessageCheck) {
                let containerMedia = lastMessageCheck.querySelector(".message-media-container");

                if (containerMedia) {
                    containerMedia.innerHTML += mediaHtml;
                }

                return;
            }
            
        }else if (fileType) {
            wholeContainer += `${buttonReply}<div class="message-media-container">`;
            if (fileType.startsWith('image')) {
                return   wholeContainer +`<div class="chat-media"><img src="${fileUrl}" alt="Obrázok" class="chat-media"></div></div></div>${createTimestamp()}`;
            } else if (fileType.startsWith('video')) {
                return  wholeContainer +`<div class="chat-media"><video controls class='chat-media'><source src="${fileUrl}" type="video/mp4"></video></div></div>`;
            } else if (fileType.startsWith('audio')) {
                return  wholeContainer +`<div class="chat-media"><audio  class="player chat-media"><source src="${fileUrl}" type="audio/mpeg"></audio></div></div>`;
            }
        }

        
        return `<div class="chat-media"><a href="${fileUrl}" target="_blank">Stiahnuť súbor</a></div>`;
            
        }

    function createTimestamp() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        return `<p class="message-time">${hours}:${minutes}</p>`;
    }
    function createTextContent() {

        return `
        <div class="details">
            <button class="reply-btn"><i class="fa-solid fa-reply"></i></button>
            <span class='message-info-cantent'> <p class ='message-content-para'>${inputMessage}</p>${createTimestamp()}</span> 
        </div>
    `
    }

    

    if (inputMessage === '') {
        contentHtml = createMediaHtml();
    } else if (fileUrl) {
        contentHtml = createTextContent() + createMediaHtml();
    } else {
        contentHtml = createTextContent();
    }

    if (parentMessageIdReply) {
        const replyMsgContent = document.querySelector(`[data-message-id="${parentMessageIdReply}"]`);
        
        const existingMedia = replyMsgContent.querySelector('.reply-message-media .chat-media');
        
    
        const replyText = replyMsgContent.querySelector('.message-content-para') ? replyMsgContent.querySelector('.message-content-para').textContent : '';
        const media = replyMsgContent.querySelector('.chat-media') ? replyMsgContent.querySelector('.chat-media').innerHTML : '';
        
        if (existingMedia) {
            parentContentHtml = `
                <div class="reply-message" data-parent-id="${parentMessageIdReply}">
                    ${replyText ? `<p>${replyText}</p>` : ''}
                </div>
            `;
            
        } else {
            parentContentHtml = `
                <div class="reply-message-media" data-parent-id="${parentMessageIdReply}">
                    ${replyText ? `<p>${replyText}</p>` : ''}
                    ${media ? `<div class="reply-message-media ">${media}</div>` : ''}
                </div>
            `;
        }
    }
    


    if (contentHtml === '') {
        console.error("Správa je prázdna a neexistuje ani mediálny obsah.");
        return;
    }else if(lastMessageCheck !=null && lastMessageCheck !='' ){
        return
    }

    messageDiv.setAttribute("data-message-id", lastMessageId);
    messageDiv.innerHTML = parentContentHtml + contentHtml;  

    messageDiv.classList.add("chat", user_id == logged_userId ? "outgoing" : "incoming");

    messageContainer.appendChild(messageDiv)
    setTimeout(() => {
        photoView ()
        scrollIntoView()

    }, 500);

    const chatContainer = document.getElementById('chat-box');
    let appendUsersMessages = document.querySelector('.chat-box');
    let appendScrollPosition = appendUsersMessages.scrollTop;
    let appendScrollHeight = appendUsersMessages.scrollHeight;  
    let appendClientHeight = appendUsersMessages.clientHeight;
    if(appendScrollPosition > (appendScrollHeight - appendClientHeight - 500)){
        chatContainer.scrollTop = messageContainer.scrollHeight;
    }


      
}

function sendMessage(chatId, logged_userId, parent_message_id) {
    const messageContent = document.getElementById('message-input').value;
    const files = document.getElementById('file-input').files; 
    if (!messageContent.trim() && files.length === 0) {
        return;
    }

    let xhr = new XMLHttpRequest();
    xhr.open('POST', './php/sendMessage.php', true);

    let formData = new FormData();
    formData.append('message', messageContent);
    formData.append('chat_id', chatId);
    formData.append('user_id', logged_userId);
    formData.append('parent_message_id', parent_message_id);

    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                let response = JSON.parse(xhr.responseText);
                
            } catch (e) {
                console.error('Chyba pri spracovávaní odpovede: ', e);
            }
        } else {
            console.error('Chyba pri odosielaní správy: ' + xhr.status);
        }
    };

    xhr.send(formData);  

    document.getElementById('message-input').value = '';
    document.getElementById('file-input').value = '';
}

function updateLastActive(userId) {
    let user_id = Number(userId);

    fetch('/php/updateLastActive.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json', 
      },
      body: JSON.stringify({ user_id: user_id })  
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Chyba na serveri: ' + response.statusText);
      }
      return response.text();  
    })
    .then(text => {
      try {
        const data = JSON.parse(text);  
      } catch (error) {
        console.error('Chyba pri spracovaní JSON:', error);
        console.error('Odpoveď nebola platný JSON:', text);
      }
    })
    .catch(error => {
      console.error('Chyba pri aktualizácii:', error);
    });
}
let inactivityTime = 25000;

let timeout;
function setAwayStatus() {
    fetch('/php/update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            status: 'away'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log("Status updated to away");
        } else {
            console.error("Error:", data.error);
        }
    })
    .catch(error => {
        console.error("Chyba v požiadavke:", error);
    });
}


function resetTimer() {
    clearTimeout(timeout);
    timeout = setTimeout(setAwayStatus, inactivityTime);
}

['mousemove', 'keydown', 'mousedown', 'touchstart'].forEach(evt =>
    document.addEventListener(evt, resetTimer)
);

resetTimer(); 
function getLastMessageId() {
    const lastMessages = document.querySelectorAll('.chat'); 
    let lastMessageId= 0;

    lastMessages.forEach(messages =>{
        const messageId= parseInt(messages.dataset.messageId);
        if (!isNaN(messageId)&& messageId > lastMessageId){
            lastMessageId = messageId;
        }
        
        
    })
    if (lastMessageId >0){
        return lastMessageId
    }else if (firstMessageId == 0.5){
        lastMessageId= 0.9
        return lastMessageId
    }else{
        lastMessageId =0
        return lastMessageId
      
        
    }

}

function getUserInfo(id) {

    id = Number(id);


    let xhr = new XMLHttpRequest();
    xhr.open('POST', './php/userInfo.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        if (xhr.status === 200) {
            try {
                let response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    let message= response.message[0];

                    let timeDifference = '';

                    if(message.status === 'Aktívny/a teraz'){
                        let userChatInfo = document.querySelector(".current-user");
                        let userImgInfo = document.querySelector(".photo-place");
                        userChatInfo.innerHTML = '';  
                        userImgInfo .innerHTML = '';

                        userImgInfo.innerHTML = `
                       <div class="photo-place">
                           <img src="${response.profile_pic}" alt="profile_pics" />
                       </div>
                        `;

                        userChatInfo.innerHTML = `
                        <div class="photo-place">
                           <img src="${response.profile_pic}" alt="profile_pics" />
                       </div>
                        <h1 data-clickedUser-id="${id}">${message.fname} ${message.lname}</h1>
                        <p class="opnStts">${message.status}</p>
                        <i id="go-back-arrow" class="fa-solid fa-arrow-left" aria-hidden="false"></i>
                    `;
                        
                    }else{
                        let lastActiveTime = null;
                        let timeDifference = '';

                        if (message.last_active && message.last_active.trim() !== '') {
                            lastActiveTime = new Date(message.last_active.replace(' ', 'T'));
                        
                            if (isNaN(lastActiveTime)) {
                                console.error('Invalid last active time:', message.last_active);
                                timeDifference = 'Invalid date';
                            } else {
                                timeDifference = formatTimeDifference(lastActiveTime);
                            }
                        } else {
                            timeDifference = ''; 
                        }
                        let userChatInfo = document.querySelector(".current-user");
                        let userImgInfo = document.querySelector(".photo-place");
                        userImgInfo .innerHTML = '';
                        userChatInfo.innerHTML = '';
                        
                        userImgInfo.innerHTML = `
                       <div class="photo-place">
                           <img src="${response.profile_pic}" alt="profile_pics" />
                       </div>
                        `;
                        userChatInfo.innerHTML = `
                        <div class="photo-place">
                           <img src="${response.profile_pic}" alt="profile_pics" />
                        </div>
                        <h1 data-clickedUser-id="${id}">${message.fname} ${message.lname}</h1>
                        <p class="opnStts"data-last-active="${timeDifference}">${timeDifference}</p>
                        <i id="go-back-arrow" class="fa-solid fa-arrow-left" aria-hidden="false"></i>
                    `;
    

                    }

                } else {
                    console.log("Chyba v getUserInfo:", response);
                }
            } catch (error) {
                console.error("Chyba pri spracovaní odpovede v getUserInfo", error);
            }
        } else {
            console.log("Chyba v statuse 200 v getUserInfo");
        }
    };
    

    xhr.send('id=' + id);
    
}

function formatTimeDifference(lastActiveTime) {
    const now = new Date();
    const diffMs = now - new Date(lastActiveTime);

    if (isNaN(diffMs)) return 'Invalid date'; 

    if (diffMs < 60000) return `Aktívny/a pred ${Math.floor(diffMs / 1000)}s`; 
    if (diffMs < 3600000) return `Aktívny/a pred ${Math.floor(diffMs / 60000)}m`;
    if (diffMs < 86400000) return `Aktívny/a pred ${Math.floor(diffMs / 3600000)}h`; 

    return `Aktívny/a pred ${Math.floor(diffMs / 86400000)}d`;
}

function changeStatusView() {
    let currentUser = document.querySelector('.current-user');
    if (!currentUser) return;

    let userStatusElement = currentUser.querySelector('.opnStts');
    if (!currentUser.querySelector('h1').getAttribute('data-clickeduser-id')) {
        return;
    }
    
    let userId = currentUser.querySelector('h1').getAttribute('data-clickeduser-id'); 
    let statusIcon = document.querySelector(`.logged-user.user[data-user-id="${userId}"] .status i`);


    if (!statusIcon || !userStatusElement ) {
        console.log('Missing one of the elements: statusIcon, userStatusElement, or timeContainer');
        return;
    }

    let lastActiveTime = userStatusElement.getAttribute('data-last-active');

    let currentStatus = '';
    let statusColor = statusIcon.style.color;

    if (statusColor === 'green' && userStatusElement !=='Aktívny/a teraz') {
        currentStatus = 'Aktívny/a teraz';
        userStatusElement.textContent = 'Aktívny/a teraz'; 
    } else if (statusColor === 'brown' && !userStatusElement.textContent.startsWith("Aktívny/a pred")) {
        currentStatus = 'away';
        userStatusElement.textContent = 'Away'; 
    }else if (statusColor === 'silver' && !userStatusElement.textContent.startsWith("Aktívny/a pred")){
        currentStatus = 'offline';
        userStatusElement.textContent = 'Offline';
    }

}
function updateActivityTime(lastActiveTime, timeContainer) {
    console.log('updateActivityTime called'); 

    if (lastActiveTime && lastActiveTime !== '0s') {
        console.log('Updating activity time for:', lastActiveTime);
        let timeDiff = formatTimeDifference(new Date(lastActiveTime));
        timeContainer.textContent = `Aktívny/a pred ${timeDiff}`;ty
    } else {
        timeContainer.textContent = `Aktívny/a pred 0s`; 
    }
}

function loadLatestMessages(chatId, logged_userId) {
    let id = Number(getLastMessageId());


    
    let noMessages= document.querySelector(".no-message-chat")
    
    if (noMessages) {

        setTimeout(() => loadLatestMessages(chatId, logged_userId),1000);
    }else if (id == 0) {
        setTimeout(() => loadLatestMessages(chatId, logged_userId), 500);
    }else {

        firstMessageId =0// pretože ajax sa nacital viackrat než sa zobrazilo a načitalo posledne pridane id

        
        let xhr = new XMLHttpRequest();
        xhr.open('POST', './php/lastMessage.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        let chatIdNum = Number(chatId);
        if (isNaN(chatIdNum)) {
            console.error("Neplatné chatId:", chatId);
            return;
        }

        const data = 'chat_id=' + chatIdNum + '&id=' + id;

        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    let response = JSON.parse(xhr.responseText);
                    if (response.getLatestMessages.status === 'success') {
                        let messageData = response.getLatestMessages.messages;

                        if (messageData && Array.isArray(messageData) && messageData.length > 0) {
                            messageData.forEach(msg => {
                                if (msg.parent_message_id !== undefined && msg.parent_message_id !== null) {
                                    appendMessage(msg.message, msg.user_id, logged_userId, msg.id, msg.file_url, msg.file_type,msg.parent_message_id);
                                } else {
                                    appendMessage(msg.message, msg.user_id, logged_userId, msg.id, msg.file_url, msg.file_type);
                                }
                            });

                            let lastMessageId = messageData[messageData.length - 1].id;
                            localStorage.setItem('lastMessageId', lastMessageId);
                        } else {
                            console.log('Žiadne nové správy.');
                        }
                    }
                } catch (error) {
                    console.error('Chyba pri spracovaní odpovede:', error);
                }
            } else {
                console.error('Chyba pri načítaní správ:', xhr.statusText);
            }
        };

        xhr.send(data);
    }
}
  
function markMessageAsRead(messageId, user_id) { 
    fetch('php/markAsRead.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            messageId: messageId,
            userId: user_id,
        })
    })
    .then(response => response.json()) 
    .then(data => {
        if (data.status === 'success') {
            const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
            messageElement.classList.add('messageRead');  
        } else {
            console.error("Chyba pri označovaní správy:", data.message);  // Zobraz chybu v konzole
        }
    })
    .catch(error => {
        console.error('Chyba pri komunikácii so serverom:', error);
    });
}

const observer = new IntersectionObserver((entries, observer) => {

    let userClassId = document.querySelector(".logged-user");
    let user_id = userClassId.getAttribute("logged-user-id");

    
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const messageId = entry.target.getAttribute('data-message-id');

            markMessageAsRead(messageId, user_id);  
            observer.unobserve(entry.target);  
        }
    });
}, { threshold: 0.5 });  

document.querySelectorAll('.message').forEach(message => {
    observer.observe(message);  
});

function handleFileUpload(event) {
    const file = event.target.files[0];
    const maxFileSize = 50 * 1024 * 1024; 

    if (file.size > maxFileSize) {
        alert('Súbor je príliš veľký! Maximálna veľkosť súboru je 50 MB.');
        event.target.value = '';
    }
}

const fileInput = document.getElementById('file-input');
fileInput.addEventListener('change', handleFileUpload);

