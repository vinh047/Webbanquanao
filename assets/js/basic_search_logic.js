const input = document.getElementById("searchInput");
const resultBox = document.getElementById("searchResultBox");

input.addEventListener("keyup", async (e) => {
  const keyword = input.value.trim();

  if (e.key === "Enter" && keyword) {
    addRecentSearch(keyword);
    window.location.href = "/index.php?page=search&q=" + encodeURIComponent(keyword);
    return;
  }

  if (!keyword) {
    renderRecentSearches();
    return;
  }

  try {
    const res = await fetch("/../ajax/basic_search_suggestions.php", {
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
          ${
            data.length > 10
              ? `<a href="/index.php?page=search&q=${encodeURIComponent(keyword)}" class="text-muted small">Xem tất cả</a>`
              : ""
          }
        </div>
        <ul class="list-unstyled ps-3">
          ${data
            .slice(0, 10)
            .map((html) => `<li class="mb-1">${html}</li>`)
            .join("")}
        </ul>

      `;
      resultBox.innerHTML = html;
    }
  } catch (err) {
    console.error("Lỗi khi tìm kiếm:", err);
    resultBox.innerHTML = `<p class="text-danger">Lỗi khi tìm kiếm.</p>`;
  }
});

// Xử lý khi người dùng click vào gợi ý tìm kiếm gần đây
document.addEventListener("click", function (e) {
  if (e.target.classList.contains("recent-key")) {
    const keyword = e.target.textContent;
    input.value = keyword;
    input.dispatchEvent(new KeyboardEvent("keyup", { bubbles: true }));
  }
});

renderRecentSearches();
