document.addEventListener("DOMContentLoaded", function () {
    const loginContainer = document.querySelector(".login-container");
    loginContainer.style.opacity = "0";
    loginContainer.style.transform = "scale(0.8)";
    
    setTimeout(() => {
        loginContainer.style.transition = "opacity 1s ease-in-out, transform 1s ease-in-out";
        loginContainer.style.opacity = "1";
        loginContainer.style.transform = "scale(1)";
    }, 300);
});
