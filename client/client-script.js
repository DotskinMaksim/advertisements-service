function logOut() {
    localStorage.removeItem('authToken');  
    localStorage.removeItem('userId');  
    sessionStorage.removeItem('authToken'); 
    sessionStorage.removeItem('userId'); 

    window.location.reload();
}
const apiHost = 'your_api_host';
