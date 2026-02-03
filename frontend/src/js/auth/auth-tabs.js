

export function initAuthTabs() {
  const auth = document.querySelector('.auth');
  if (!auth) return;

  const tabs = auth.querySelectorAll('.auth__tab');
  const panels = auth.querySelectorAll('.auth__panel');
  if (!tabs.length || !panels.length) return;

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      if (tab.classList.contains('auth__tab--active')) return;

      const targetId = tab.getAttribute('aria-controls');
      const targetPanel = targetId ? auth.querySelector(`#${targetId}`) : null;
      if (!targetPanel) return;

      tabs.forEach((t) => {
        t.classList.remove('auth__tab--active');
        t.setAttribute('aria-selected', 'false');
      });

      panels.forEach((panel) => {
        panel.classList.remove('auth__panel--active');
        panel.hidden = true;
      });

      tab.classList.add('auth__tab--active');
      tab.setAttribute('aria-selected', 'true');

      targetPanel.classList.add('auth__panel--active');
      targetPanel.hidden = false;
    });
  });
}
