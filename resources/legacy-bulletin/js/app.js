(function () {
  const pixButton = document.querySelector("[data-pix]");
  const navLinks = document.querySelectorAll(".topbar__nav a[href^='#']");
  const sections = [...navLinks]
    .map((link) => document.querySelector(link.getAttribute("href")))
    .filter(Boolean);

  if (pixButton) {
    const originalHtml = pixButton.innerHTML;

    pixButton.addEventListener("click", async () => {
      const pix = pixButton.dataset.pix;

      try {
        await navigator.clipboard.writeText(pix);
        pixButton.textContent = "PIX copiado";
      } catch {
        pixButton.textContent = pix;
      }

      window.setTimeout(() => {
        pixButton.innerHTML = originalHtml;
      }, 1800);
    });
  }

  const setActiveLink = () => {
    const fromTop = window.scrollY + 96;
    let current = sections[0];

    sections.forEach((section) => {
      if (section.offsetTop <= fromTop) {
        current = section;
      }
    });

    navLinks.forEach((link) => {
      const isActive = current && link.getAttribute("href") === `#${current.id}`;
      link.classList.toggle("is-active", Boolean(isActive));
    });
  };

  window.addEventListener("scroll", setActiveLink, { passive: true });
  setActiveLink();
})();
