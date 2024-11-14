function logOut() {
    localStorage.removeItem('authToken');  
    localStorage.removeItem('userId');  
    sessionStorage.removeItem('authToken'); 
    sessionStorage.removeItem('userId'); 

    window.location.reload();
}
const apiHost = 'localhost:8000/api';