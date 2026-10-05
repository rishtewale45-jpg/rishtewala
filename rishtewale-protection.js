/* Rishtewale Frontend Protection
   NOTE: This only discourages casual copying. Browser-visible content
   can never be made 100% impossible to copy.
*/
(function () {
  "use strict";

  // Disable common casual-copy actions.
  document.addEventListener("contextmenu", function (e) {
    e.preventDefault();
  }, { passive: false });

  document.addEventListener("selectstart", function (e) {
    if (!e.target.closest("input, textarea, [contenteditable='true']")) {
      e.preventDefault();
    }
  }, { passive: false });

  document.addEventListener("dragstart", function (e) {
    if (e.target && (e.target.tagName === "IMG" || e.target.tagName === "A")) {
      e.preventDefault();
    }
  }, { passive: false });

  // Block common save/view-source/devtools shortcuts.
  document.addEventListener("keydown", function (e) {
    const key = String(e.key || "").toLowerCase();
    const blocked =
      key === "f12" ||
      (e.ctrlKey && e.shiftKey && ["i", "j", "c"].includes(key)) ||
      (e.ctrlKey && ["u", "s"].includes(key));

    if (blocked) {
      e.preventDefault();
      e.stopPropagation();
    }
  }, true);

  // Prevent images from being dragged in browsers.
  document.querySelectorAll("img").forEach(function (img) {
    img.setAttribute("draggable", "false");
  });

  // Apply to dynamically added images too.
  const observer = new MutationObserver(function () {
    document.querySelectorAll("img:not([draggable='false'])").forEach(function (img) {
      img.setAttribute("draggable", "false");
    });
  });
  observer.observe(document.documentElement, { childList: true, subtree: true });
})();
