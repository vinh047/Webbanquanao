function getRecentSearches() {
  return JSON.parse(localStorage.getItem("recent_searches") || "[]");
}

function addRecentSearch(keyword) {
  let searches = getRecentSearches();
  keyword = keyword.trim();
  if (!keyword) return;
  searches = searches.filter(k => k.toLowerCase() !== keyword.toLowerCase());
  searches.unshift(keyword);
  if (searches.length > 5) searches.pop();
  localStorage.setItem("recent_searches", JSON.stringify(searches));
}

function renderRecentSearches() {
  const recent = getRecentSearches();
  const resultBox = document.getElementById("searchResultBox");
  if (recent.length === 0) {
    resultBox.innerHTML = "<p style='color:gray;'>Không có tìm kiếm gần đây</p>";
    return;
  }
  resultBox.innerHTML = "<p style='color:gray;'>Tìm kiếm gần đây</p><div style='display: flex; flex-wrap: wrap; gap: 10px;'>" +
    recent.map(k => `<button class="suggestion-button recent-key">${k}</button>`).join('') + "</div>";
}