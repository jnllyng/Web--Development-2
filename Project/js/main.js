const headers = document.querySelectorAll("th.sortable");

headers.forEach(header => {
  header.addEventListener("click", () => {
    headers.forEach(h => h.classList.remove("active-sort"));
    header.classList.add("active-sort");

    const column = header.dataset.column;
    console.log(`Sorting by ${column}`);
  });
});
