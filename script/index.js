document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.createElement("button");
    menuToggle.classList.add("menu-toggle");
    menuToggle.innerHTML = "☰";
    document.querySelector(".nav-container").appendChild(menuToggle);
  
    const navMenu = document.querySelector(".nav-menu");
  
    menuToggle.addEventListener("click", () => {
      navMenu.classList.toggle("active");
    });
  });
  