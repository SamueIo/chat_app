const searchBar = document.querySelector(".searching-users-input input"),
searchBtn = document.querySelector(".searching-users-button button");
usersList = document.querySelector(".users-list");
let     = document.querySelector(".logged-user")

searchBtn.onclick = () => {
    searchBar.classList.toggle("active");
    searchBar.focus();
    searchBtn.classList.toggle("active");

}
searchBar.onkeyup = ()=>{

    let searchTerm = searchBar.value;
    if(searchTerm !=""){
        searchBar.classList.add("active");
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "php/search.php", true);
    
        xhr.onload = () => {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                let data = xhr.response.trim();
                usersList.innerHTML = data;

            }
        }
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("searchTerm=" + encodeURIComponent(searchTerm));
    }else{
        searchBar.classList.remove("active");
    }
    
}

setInterval(()=>{
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "php/users.php", true);

    xhr.onload = () => {
        
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            let data = xhr.response.trim();

            if(!searchBar.classList.contains("active")){
                usersList.innerHTML = data;
            }
        }
    }
    xhr.send();
},1000)

