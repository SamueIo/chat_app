let currentImageIndex = 0; 
let images = []; 

function photoView(fileUrl) {
    let mediaContainer = document.querySelectorAll(".chat-media img");
    images = []; 

    
    if (fileUrl != null) {
        photoView()
    }

    mediaContainer.forEach((media, index) => {
        images.push(media.src); 
        media.addEventListener("click", () => {
            openModal(index); 
        });
    });
    
}

function openModal(index) {
    currentImageIndex = index; 
    const modal = document.getElementById("photoModal");
    const modalImage = document.getElementById("modalImage");
    const caption = document.getElementById("caption");

    modalImage.src = images[currentImageIndex]; 
    modalImage.onload = () => {
        modal.style.display = "flex"; 
    }

    caption.textContent = "Obrázok " + (currentImageIndex + 1); 
}


function closeModal() {
    const modal = document.getElementById("photoModal");
    modal.style.display = "none";
}


function changeImage(direction) {
    currentImageIndex += direction;

    if (currentImageIndex < 0) {
        currentImageIndex = images.length - 1;
    }
    if (currentImageIndex >= images.length) {
        currentImageIndex = 0;
    }

    const modalImage = document.getElementById("modalImage");
    const caption = document.getElementById("caption");

    modalImage.src = images[currentImageIndex];
    caption.textContent = "Obrázok " + (currentImageIndex + 1);
}

window.addEventListener("click", function(event) {
    const modal = document.getElementById("photoModal");
    if (event.target == modal) {
        closeModal();
    }
});

