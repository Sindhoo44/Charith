function loadPage(page) {
    const div = document.getElementById("div1");
    const section = document.getElementById("contentSection");

    div.style.display = 'none';

    fetch(page)
        .then(response => response.text())
        .then(data => {
            div.innerHTML = data;
            div.style.display = 'block';

            // 🔁 Small delay ensures content is visible before scrolling
            setTimeout(() => {
                section.scrollIntoView({
                    behavior: "smooth"
                });
            }, 150);
        })
        .catch(error => console.error('Error loading content:', error));
}

// // Attach event listeners
// document.getElementById("home").onclick = () => loadPage('home.html');
// document.getElementById("explore").onclick = () => loadPage('explore.html');
// document.getElementById("booking").onclick = () => loadPage('book.html');

document.getElementById("countrySelect").addEventListener("change", function () {
    const selectedCode = this.value;
    document.getElementById("countryCode").textContent = selectedCode;
});
