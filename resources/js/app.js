import Sortable from "sortablejs";

for (const form of document.querySelectorAll("[data-confirm]")) {
    form.addEventListener("submit", (event) => {
        if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
}

const list = document.querySelector("#task-list");
if (list) {
    const status = document.querySelector("#order-status");
    let saving = false;
    let failed = false;
    let previousOrder = [];
    const ids = () => [...list.children].map((row) => Number(row.dataset.id));
    const renumber = () =>
        [...list.children].forEach((row, i) => {
            row.querySelector(".priority").textContent = i + 1;
            row.querySelector('[data-direction="-1"]').disabled =
                saving || failed || i === 0;
            row.querySelector('[data-direction="1"]').disabled =
                saving || failed || i === list.children.length - 1;
        });
    const sortable = Sortable.create(list, {
        handle: ".handle",
        animation: 150,
        ghostClass: "drag-ghost",
        onStart: () => {
            previousOrder = ids();
        },
        onEnd: () => {
            if (ids().join() !== previousOrder.join()) saveOrder();
        },
    });
    async function saveOrder() {
        saving = true;
        sortable.option("disabled", true);
        list.setAttribute("aria-busy", "true");
        renumber();
        status.textContent = "Saving priority…";
        try {
            const response = await fetch(list.dataset.url, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                },
                body: JSON.stringify({
                    task_ids: ids(),
                    version: Number(list.dataset.version),
                }),
            });
            if (!response.ok) throw new Error("Save failed");
            const result = await response.json();
            list.dataset.version = result.version;
            status.textContent = "Priority saved.";
        } catch {
            sortable.sort(previousOrder.map(String));
            failed = true;
            status.textContent =
                "Could not save priority. Reload the page to get the latest order and try again.";
            status.classList.add("error");
        } finally {
            saving = false;
            list.removeAttribute("aria-busy");
            sortable.option("disabled", failed);
            renumber();
        }
    }
    list.addEventListener("click", (event) => {
        const button = event.target.closest(".move");
        if (!button || saving || failed) return;
        const row = button.closest("li");
        const direction = Number(button.dataset.direction);
        const sibling =
            direction === -1
                ? row.previousElementSibling
                : row.nextElementSibling;
        if (!sibling) return;
        previousOrder = ids();
        if (direction === -1) list.insertBefore(row, sibling);
        else list.insertBefore(sibling, row);
        saveOrder();
    });
    renumber();
}
