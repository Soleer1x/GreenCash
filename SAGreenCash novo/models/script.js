// Scroll header background
window.addEventListener('scroll', function() {
    const header = document.getElementById('header');
    if (window.scrollY > 80) {
      header.style.background = '#ffffff';
      header.style.boxShadow = '0 4px 10px rgba(0,0,0,0.1)';
    } else {
      header.style.background = 'transparent';
      header.style.boxShadow = 'none';
    }
  });
  
  // Dark Mode Toggle
  const toggleTheme = document.getElementById('toggle-theme');
  toggleTheme.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    const icon = toggleTheme.querySelector('i');
    setTimeout(() => {
      if (document.body.classList.contains('dark-mode')) {
        icon.classList.replace('fa-moon', 'fa-sun');
      } else {
        icon.classList.replace('fa-sun', 'fa-moon');
      }
    }, 200);
  });
  