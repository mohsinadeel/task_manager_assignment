import Sortable from "sortablejs";

for (const form of document.querySelectorAll("[data-confirm]")) {
    form.addEventListener("submit", (event) => {
        if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
}
