function createParticles() {
    const particlesContainer = document.getElementById('particles');
    const colors = ['#60a5fa', '#818cf8', '#a78bfa', '#c084fc', '#e879f9', '#f472b6'];
    
    for (let i = 0; i < 30; i++) {
      const particle = document.createElement('div');
      const size = Math.random() * 20 + 5;
      
      particle.className = 'particle';
      particle.style.width = `${size}px`;
      particle.style.height = `${size}px`;
      particle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
      particle.style.left = `${Math.random() * 100}%`;
      particle.style.top = `${Math.random() * 100}%`;
      particle.style.opacity = `${Math.random() * 0.2 + 0.1}`;
      
      // Randomize animation properties
      const duration = Math.random() * 20 + 10;
      const delay = Math.random() * 10;
      particle.style.animation = `float ${duration}s infinite linear ${delay}s`;
      
      particlesContainer.appendChild(particle);
    }
  }
  
  // Initialize the particles
  createParticles();