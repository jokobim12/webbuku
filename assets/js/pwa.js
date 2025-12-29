if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js').then(reg => {
      console.log('Service Worker registered:', reg.scope);
    }).catch(err => console.error('SW registration failed:', err));
  });
}

let deferredPrompt;
const installUI = document.createElement('div');
installUI.id = 'pwa-install-ui';
installUI.style.position = 'fixed';
installUI.style.right = '16px';
installUI.style.bottom = '16px';
installUI.style.zIndex = '9999';
installUI.style.display = 'none';

installUI.innerHTML = `
  <div style="background: rgba(0,0,0,0.7); color: white; padding: 10px 12px; border-radius: 10px; box-shadow: 0 6px 18px rgba(0,0,0,0.2); display:flex; gap:8px; align-items:center">
    <div style="font-size:18px">📚</div>
    <div style="font-size:14px">Pasang WebBuku ke perangkat Anda</div>
    <button id="pwa-install-btn" style="background:#10b981;border:none;color:white;padding:6px 10px;border-radius:8px;font-weight:600;cursor:pointer">Pasang</button>
  </div>
`;

document.body.appendChild(installUI);

window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;
  installUI.style.display = 'block';
});

document.addEventListener('click', (e) => {
  if (e.target && e.target.id === 'pwa-install-btn') {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    deferredPrompt.userChoice.then((choiceResult) => {
      if (choiceResult.outcome === 'accepted') {
        console.log('User accepted the install prompt');
      } else {
        console.log('User dismissed the install prompt');
      }
      deferredPrompt = null;
      installUI.style.display = 'none';
    });
  }
});

window.addEventListener('appinstalled', () => {
  console.log('PWA installed');
  installUI.style.display = 'none';
});
