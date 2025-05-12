const input = document.getElementById("searchInput");
const resultBox = document.getElementById("searchResultBox");

input.addEventListener("keyup", async (e) => {
  const keyword = input.value.trim();

  if (e.key === "Enter" && keyword) {
    addRecentSearch(keyword);
    window.location.href = "/search.html?q=" + encodeURIComponent(keyword);
    return;
  }

  if (!keyword) {
    renderRecentSearches();
    return;
  }

  try {
    const res = await fetch("search_suggestions.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ keyword })
    });

    const data = await res.json();
    if (data.length === 0) {
      resultBox.innerHTML = `<p class="text-muted">Không tìm thấy kết quả.</p>`;
    } else {
      let html = `
        <div class="d-flex justify-content-between align-items-center mb-2">
          <p class="mb-0 fw-bold">Sản phẩm (${data.length} kết quả)</p>
          ${data.length > 10
            ? `<a href="/search.html?q=${encodeURIComponent(keyword)}" class="text-muted small">Xem tất cả</a>`
            : ""}
        </div>
        <ul class="list-unstyled ps-3">
          ${data.slice(0, 10).map(name => `<li class="mb-1">${name}</li>`).join("")}
        </ul>
      `;
      resultBox.innerHTML = html;
    }
  } catch (err) {
    resultBox.innerHTML = `<p class="text-danger">Lỗi khi tìm kiếm.</p>`;
  }
});

document.addEventListener('click', function (e) {
  if (e.target.classList.contains('recent-key')) {
    const keyword = e.target.textContent;
    input.value = keyword;
    input.dispatchEvent(new KeyboardEvent('keyup', { bubbles: true }));
  }
});

renderRecentSearches();
