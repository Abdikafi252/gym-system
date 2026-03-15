(function () {
  window.showToast = function (message, type) {
    type = type || 'success';
    var bg = type === 'error' ? '#dc2626' : (type === 'warning' ? '#d97706' : '#16a34a');

    var toast = document.createElement('div');
    toast.textContent = message;
    toast.style.position = 'fixed';
    toast.style.right = '16px';
    toast.style.top = '16px';
    toast.style.zIndex = '99999';
    toast.style.padding = '10px 14px';
    toast.style.color = '#fff';
    toast.style.background = bg;
    toast.style.borderRadius = '8px';
    toast.style.boxShadow = '0 8px 20px rgba(0,0,0,.2)';
    toast.style.fontSize = '13px';
    toast.style.fontWeight = '700';

    document.body.appendChild(toast);
    setTimeout(function () { toast.remove(); }, 2600);
  };
})();
