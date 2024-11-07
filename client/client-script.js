function logOut() {
    // Удаление токена из localStorage или sessionStorage
    localStorage.removeItem('authToken');  // если вы сохраняете токен в localStorage
    localStorage.removeItem('userId');  // если вы сохраняете токен в localStorage
    sessionStorage.removeItem('authToken'); // если вы сохраняете токен в sessionStorage
    sessionStorage.removeItem('userId'); // если вы сохраняете токен в sessionStorage

    // Перенаправление на главную страницу после выхода
    window.location.href = '../client';  // или на другую страницу, например, на страницу входа

}
