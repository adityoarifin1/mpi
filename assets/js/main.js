document.addEventListener('DOMContentLoaded', () => {
  const clock = document.getElementById('clock');
  if (clock) {
    const updateClock = () => {
      const now = new Date();
      const time = now.toLocaleTimeString('id-ID', { hour12: false });
      clock.textContent = time;
    };
    updateClock();
    setInterval(updateClock, 1000);
  }

  const toastData = document.getElementById('toastData');
  if (toastData && toastData.dataset.message) {
    showToast(toastData.dataset.message, 'success');
  }
});

function showToast(message, type = 'success') {
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: type,
      title: message,
      showConfirmButton: false,
      timer: 2200,
      timerProgressBar: true,
      customClass: {
        popup: 'swal2-toast-custom'
      }
    });
  }
}
