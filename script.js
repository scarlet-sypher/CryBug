document.addEventListener('DOMContentLoaded', function() {
  // =============== SHOW MENU ===============
  const navMenu = document.getElementById('nav-menu');
  const navToggle = document.getElementById('nav-toggle');
  const navClose = document.getElementById('nav-close');

  // Menu show
  if (navToggle) {
      navToggle.addEventListener('click', () => {
          navMenu.style.top = '0';
      });
  }

  // Menu hidden
  if (navClose) {
      navClose.addEventListener('click', () => {
          navMenu.style.top = '-100%';
      });
  }

  // Close mobile menu when clicking on a nav link (except dropdowns)
  const navLinks = document.querySelectorAll('.nav-menu a:not(.dropdown-trigger)');
  navLinks.forEach(link => {
      link.addEventListener('click', () => {
          // Only apply to mobile view
          if (window.innerWidth < 768) {
              navMenu.style.top = '-100%';
          }
      });
  });

  // Add this new event listener for screen resize
  window.addEventListener('resize', () => {
      if (window.innerWidth >= 768) {
          navMenu.style.top = '0'; // Reset position when screen is desktop size
          
          // Reset dropdown styles when switching to desktop
          document.querySelectorAll('.has-dropdown').forEach(item => {
              item.classList.remove('active');
          });
          document.querySelectorAll('.dropdown-trigger').forEach(trigger => {
              trigger.classList.remove('active');
          });
      } else {
          navMenu.style.top = '-100%'; // Hide when resizing to mobile if it's not already shown
      }
  });

  // =============== SEARCH ===============
  const search = document.getElementById('search');
  const searchBtn = document.getElementById('search-btn');
  const searchClose = document.getElementById('search-close');
  const searchForm = document.querySelector('.search-form');
  const searchInput = document.getElementById('search-input');
  const searchSubmit = document.getElementById('search-submit');
  const searchResults = document.getElementById('search-results');
  const searchMessage = document.getElementById('search-message');
  const searchResultsList = document.getElementById('search-results-list');
  
  
  const searchData = [
      { title: 'Home Page', url: '#home', description: 'Main landing page' },
      { title: 'About Us', url: '#about-us', description: 'Learn about our company' },
      { title: 'Premium Service', url: '#service1', description: 'Our best-in-class service offering' },
      { title: 'Quick Launch Service', url: '#service2', description: 'Fast deployment solutions' },
      { title: 'Interactive Support', url: '#service3', description: '24/7 customer assistance' },
      { title: 'Security Audit', url: '#service4', description: 'Comprehensive security assessment' },
      { title: 'Product Alpha', url: '#product1', description: 'Our flagship product' },
      { title: 'Suite Beta', url: '#product2', description: 'Complete software suite' },
      { title: 'Dev Tools', url: '#product3', description: 'Tools for developers' },
      { title: 'Analytics Pro', url: '#product4', description: 'Advanced analytics platform' },
      { title: 'Contact', url: '#contact', description: 'Get in touch with us' }
  ];
  
  // Make sure search button is visible and working
  if (searchBtn) {
      // Add a fallback if the icon isn't visible
      if (searchBtn.querySelector('i') && !searchBtn.querySelector('i').offsetWidth) {
          searchBtn.innerHTML = '<span style="font-size: 24px">🔍</span>';
      }
      
      searchBtn.addEventListener('click', function(event) {
          event.preventDefault();
          event.stopPropagation();
          
          if (search) {
              // Add entrance animation
              search.style.opacity = '1';
              search.style.pointerEvents = 'auto';
              
              // Animate search form
              if (searchForm) {
                  searchForm.classList.add('scale-100');
                  searchForm.classList.remove('scale-95');
              }
              
              // Focus input with delay for better UX
              if (searchInput) {
                  setTimeout(() => {
                      searchInput.focus();
                  }, 300);
              }
          }
      });
  }
  
  // Search close button handler
  if (searchClose) {
      searchClose.addEventListener('click', function(event) {
          event.preventDefault();
          event.stopPropagation();
          closeSearch();
      });
  }
  
  // Search form submission
  if (searchForm) {
      searchForm.addEventListener('submit', function(event) {
          event.preventDefault();
          performSearch();
      });
  }
  
  // Search submit button click
  if (searchSubmit) {
      searchSubmit.addEventListener('click', function(event) {
          event.preventDefault();
          performSearch();
      });
  }
  
  // Enhanced search functionality
  function performSearch() {
      const query = searchInput.value.toLowerCase().trim();
      
      if (query === '') {
          searchResults.classList.remove('active');
          setTimeout(() => {
              searchResults.style.maxHeight = '0';
              searchResults.style.opacity = '0';
          }, 100);
          return;
      }
      
      const results = searchData.filter(item => 
          item.title.toLowerCase().includes(query) || 
          item.description.toLowerCase().includes(query)
      );
      
      // Show results container
      searchResults.style.maxHeight = '400px';
      searchResults.style.opacity = '1';
      searchResults.classList.add('active');
      
      // Display results with enhanced styling
      searchResultsList.innerHTML = '';
      
      if (results.length > 0) {
          document.getElementById('search-results-title').textContent = 'Search Results';
          searchMessage.textContent = `Found ${results.length} result(s) for "${query}"`;
          
          results.forEach(result => {
              const li = document.createElement('li');
              li.innerHTML = `
                  <a href="${result.url}" class="flex flex-col">
                      <span class="font-semibold">${result.title}</span>
                      <span class="text-slate-400 text-sm">${result.description}</span>
                  </a>
              `;
              searchResultsList.appendChild(li);
          });
      } else {
          // Enhanced not found message
          document.getElementById('search-results-title').textContent = 'No Results Found';
          searchMessage.innerHTML = `No results found for <span class="text-white font-semibold">"${query}"</span>. Please try another search term.`;
          
          // Add shake animation to search input
          searchInput.classList.add('shake');
          
          // Remove animation class after it completes
          setTimeout(() => {
              searchInput.classList.remove('shake');
          }, 600);
      }
  }
  
  // Close search when clicking on the background
  if (search) {
      search.addEventListener('click', function(event) {
          // Only close if clicking directly on the search background
          if (event.target === search) {
              closeSearch();
          }
      });
  }
  
  // Prevent clicks on the form from closing the search
  if (searchForm) {
      searchForm.addEventListener('click', function(event) {
          event.stopPropagation();
      });
  }
  
  // Close search when clicking on result links
  document.addEventListener('click', function(event) {
      if (event.target.closest('#search-results-list a')) {
          closeSearch();
      }
  });
  
  // Function to close search
  function closeSearch() {
      if (search) {
          search.style.opacity = '0';
          search.style.pointerEvents = 'none';
          
          if (searchForm) {
              searchForm.classList.add('scale-95');
              searchForm.classList.remove('scale-100');
          }
          
          // Hide results and clear input
          setTimeout(() => {
              searchResults.style.maxHeight = '0';
              searchResults.style.opacity = '0';
              searchResults.classList.remove('active');
              searchInput.value = '';
          }, 300);
      }
  }
  
  // Close search with Escape key
  document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape' && search && search.style.opacity === '1') {
          closeSearch();
      }
  });

  // =============== DROPDOWN FOR MOBILE AND DESKTOP NAVIGATION ===============
  const dropdownTriggers = document.querySelectorAll('.dropdown-trigger');
  
  dropdownTriggers.forEach(trigger => {
      trigger.addEventListener('click', function(e) {
          e.preventDefault();
          const parent = this.closest('.has-dropdown');
          
          // Toggle active class for this dropdown
          parent.classList.toggle('active');
          this.classList.toggle('active');
          
          // Close other dropdowns
          dropdownTriggers.forEach(otherTrigger => {
              if (otherTrigger !== trigger) {
                  otherTrigger.closest('.has-dropdown').classList.remove('active');
                  otherTrigger.classList.remove('active');
              }
          });
      });
  });

  // Close dropdowns when clicking outside
  document.addEventListener('click', function(event) {
      if (!event.target.closest('.has-dropdown')) {
          dropdownTriggers.forEach(trigger => {
              trigger.closest('.has-dropdown').classList.remove('active');
              trigger.classList.remove('active');
          });
      }
  });

  // =============== SCROLL SPY FOR ACTIVE NAV ITEMS ===============
  const sections = document.querySelectorAll('section[id]');
  const navItems = document.querySelectorAll('.nav-item a');
  let isManuallyClicked = false;

  // Function to highlight active navigation items based on scroll position
  function scrollActive() {
      // Skip this update if triggered by manual click (will be handled by click handler)
      if (isManuallyClicked) {
          isManuallyClicked = false;
          return;
      }
      
      const scrollY = window.pageYOffset;
      
      sections.forEach(current => {
          const sectionHeight = current.offsetHeight;
          const sectionTop = current.offsetTop - 100; // Adjust offset as needed
          const sectionId = current.getAttribute('id');
          
          if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
              // Add active class to matching nav item
              navItems.forEach(item => {
                  item.classList.remove('active');
                  if (item.getAttribute('href') === `#${sectionId}`) {
                      item.classList.add('active');
                      
                      // Move the active indicator based on which item is active
                      updateNavIndicator(item);
                  }
              });
          }
      });
      
      // If at the top of the page, set "Home" as active by default
      if (scrollY === 0 || scrollY < 100) {
          navItems.forEach(item => {
              item.classList.remove('active');
              if (item.getAttribute('href') === '#' || item.getAttribute('href') === '#home') {
                  item.classList.add('active');
                  
                  // Position indicator on home
                  updateNavIndicator(item);
              }
          });
      }
  }

  function updateNavIndicator(activeItem) {
      const indicator = document.querySelector('.nav-active-indicator');
      if (indicator && window.innerWidth >= 768) {
          const li = activeItem.parentElement;
          const ulChildren = Array.from(li.parentElement.children);
          const index = ulChildren.indexOf(li);
          
          // Set position based on which item is active
          switch(index) {
              case 0:
                  indicator.style.left = '0';
                  indicator.style.width = '90px';
                  break;
              case 1:
                  indicator.style.left = '90px';
                  indicator.style.width = '95px';
                  break;
              case 2:
                  indicator.style.left = '190px';
                  indicator.style.width = '110px';
                  break;
              case 3:
                  indicator.style.left = '300px';
                  indicator.style.width = '115px';
                  break;
              case 4:
                  indicator.style.left = '420px';
                  indicator.style.width = '100px';
                  break;
          }
      }
  }

  // Add click event listeners to nav items
  navItems.forEach(item => {
      item.addEventListener('click', function(e) {
          // Set flag to prevent scroll handler from overriding our active state
          isManuallyClicked = true;
          
          // Remove active class from all items
          navItems.forEach(navItem => navItem.classList.remove('active'));
          
          // Add active class to clicked item
          this.classList.add('active');
          
          // Update active indicator
          updateNavIndicator(this);
      });
  });

  window.addEventListener('scroll', scrollActive);

  // Call once on load to set initial active state
  scrollActive();

  // =============== DROPDOWN PROFILE ===============
  const dropdown = document.getElementById('dropdown');
  if (dropdown) {
      dropdown.addEventListener('click', (event) => {
          event.stopPropagation(); // Prevent event from bubbling up
          const dropdownList = dropdown.querySelector('.dropdown-list');
          
          // Toggle dropdown visibility classes
          if (dropdownList.classList.contains('opacity-0')) {
              dropdownList.classList.remove('opacity-0', 'translate-y-[-12px]', 'pointer-events-none');
              dropdownList.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');
          } else {
              dropdownList.classList.add('opacity-0', 'translate-y-[-12px]', 'pointer-events-none');
              dropdownList.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
          }
      });
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', (event) => {
      const dropdown = document.getElementById('dropdown');
      const dropdownList = document.querySelector('.dropdown-list');
      
      if (dropdown && dropdownList && 
          !dropdown.contains(event.target) && 
          dropdownList.classList.contains('opacity-100')) {
          dropdownList.classList.add('opacity-0', 'translate-y-[-12px]', 'pointer-events-none');
          dropdownList.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
      }
  });

  // Ensure active states are properly set on page load
  window.addEventListener('load', function() {
      // Set initial active link on page load
      const currentHash = window.location.hash || '#home';
      const initialActiveLink = document.querySelector(`.nav-link[href="${currentHash}"]`);
      
      if (initialActiveLink) {
          document.querySelectorAll('.nav-link').forEach(link => {
              link.classList.remove('active');
          });
          initialActiveLink.classList.add('active');
      }
      
      // Position indicator for desktop
      if (window.innerWidth >= 768) {
          const indicator = document.querySelector('.nav-hover-indicator');
          const activeLink = document.querySelector('.nav-link.active');
          
          if (indicator && activeLink) {
              updateNavIndicator(activeLink);
          }
      }
  });

//======================================================== Typing Animation Start ===========================================================================================

    const phrases = [
        "track bugs effortlessly.",
        "manage tasks smartly.",
        "boost team productivity.",
        "resolve issues faster.",
        "deliver projects on time."
    ];
    
    const rotatingElement = document.querySelector('.rotating-text');
    const rotatingWrapper = document.querySelector('.rotating-text-wrapper');
    let currentPhraseIndex = 0;
    let currentCharIndex = 0;
    let isDeleting = false;
    
    // Set a fixed width based on the longest phrase
    function setFixedWidth() {
        // Find the longest phrase
        let maxWidth = 0;
        const tempSpan = document.createElement('span');
        tempSpan.style.visibility = 'hidden';
        tempSpan.style.position = 'absolute';
        tempSpan.style.fontSize = window.getComputedStyle(rotatingElement).fontSize;
        tempSpan.style.fontFamily = window.getComputedStyle(rotatingElement).fontFamily;
        tempSpan.style.fontWeight = window.getComputedStyle(rotatingElement).fontWeight;
        document.body.appendChild(tempSpan);
        
        phrases.forEach(phrase => {
            tempSpan.textContent = phrase;
            const width = tempSpan.getBoundingClientRect().width;
            if (width > maxWidth) {
                maxWidth = width;
            }
        });
        
        document.body.removeChild(tempSpan);
        rotatingWrapper.style.minWidth = `${maxWidth}px`;
    }
    
    function type() {
        const currentPhrase = phrases[currentPhraseIndex];
        
        if (isDeleting) {
            currentCharIndex--;
        } else {
            currentCharIndex++;
        }
        
        rotatingElement.textContent = currentPhrase.substring(0, currentCharIndex);
        
        let delay = isDeleting ? 40 : 100;
        
        if (!isDeleting && currentCharIndex === currentPhrase.length) {
            delay = 1500;
            isDeleting = true;
        } else if (isDeleting && currentCharIndex === 0) {
            isDeleting = false;
            currentPhraseIndex = (currentPhraseIndex + 1) % phrases.length;
            delay = 500;
        }
        
        setTimeout(type, delay);
    }
    
    // Initialize the fixed width first, then start typing
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            setFixedWidth();
            setTimeout(type, 800);
        }, 100);
    });
    
    // Recalculate the width if window is resized
    window.addEventListener('resize', setFixedWidth);


//======================================================== Typing Animation Ends ===========================================================================================


//======================================================== Spider Animation Start ===========================================================================================

// Initial state for responsive tracking
let wasMobile = window.innerWidth <= 767;

// Get all spider containers
const spiderContainers = document.querySelectorAll('.hanging-spider');

// Toggle paused on thread/spider image when clicked
spiderContainers.forEach((container) => {
  container.addEventListener('click', function () {
    const thread = this.querySelector('.web-thread');
    const spider = this.querySelector('.spider-image');
    thread.classList.toggle('paused');
    spider.classList.toggle('paused');
  });
});

// Hover Fall + Climb (Large Screens Only)
    const spiders = document.querySelectorAll('.hanging-spider');

    spiders.forEach((spider, index) => {
    let timeout;

    spider.addEventListener('mouseenter', () => {
        if (window.innerWidth > 1024) {
        timeout = setTimeout(() => {
            spider.classList.add('fall-hover');
            spider.classList.remove('climb-hover');
        }, index * 300);
        }
    });

    spider.addEventListener('mouseleave', () => {
        if (window.innerWidth > 1024) {
        clearTimeout(timeout);
        setTimeout(() => {
            spider.classList.remove('fall-hover');
            spider.classList.add('climb-hover');
        }, 400);
        }
    });
    });



// Handle screen resize behavior
function handleResize() {
  const isMobile = window.innerWidth <= 767;

  spiders.forEach((spider) => {
    // Remove existing animations
    spider.classList.remove('fall-hover', 'climb-hover', 'fall-mobile', 'climb-up');

    if (isMobile) {
      // On mobile, apply full fall
      spider.classList.add('fall-mobile');
    } else {
      // On returning to large screen
      if (wasMobile) {
        spider.classList.add('climb-up');
        setTimeout(() => {
          spider.classList.remove('climb-up');
        }, 2000);
      }

      // Reattach hover listeners (ensures reactivity)
      spider.addEventListener('mouseenter', () => {
        if (window.innerWidth > 1024) {
          spider.classList.add('fall-hover');
          spider.classList.remove('climb-hover');
        }
      });

      spider.addEventListener('mouseleave', () => {
        if (window.innerWidth > 1024) {
          setTimeout(() => {
            spider.classList.remove('fall-hover');
            spider.classList.add('climb-hover');
          }, 400);
        }
      });
    }
  });

  wasMobile = isMobile;
}

// Attach listeners
window.addEventListener('resize', handleResize);
window.addEventListener('load', handleResize);

});

//======================================================== Spider Animation Ends ===========================================================================================


//======================================================== Button Animation starts ===========================================================================================

document.addEventListener('DOMContentLoaded', function() {
  const buttons = document.querySelectorAll('.btn-effect');

  buttons.forEach(btn => {
    btn.addEventListener('mousemove', function(e) {
      const rect = btn.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;

      btn.style.setProperty('--x', x + 'px');
      btn.style.setProperty('--y', y + 'px');
    });
  });
});
//======================================================== Button Animation ends ===========================================================================================



//======================================================== Process Section Animation starts ===========================================================================================

document.addEventListener('DOMContentLoaded', function() {
  
  const stepDots = document.querySelectorAll('.crybug-step-dot');
  const processCards = document.querySelectorAll('.crybug-process-card');
  const expandBtns = document.querySelectorAll('.crybug-expand-btn');
  const demoButton = document.getElementById('crybug-demo-button');



  expandBtns.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      const card = this.closest('.crybug-process-card');
      const content = card.querySelector('.crybug-expandable-content');
      
      if (content.classList.contains('expanded')) {
        content.classList.remove('expanded');
        this.textContent = 'Learn more';
        
        setTimeout(() => {
          content.classList.add('hidden');
        }, 500);
      } else {
        content.classList.remove('hidden');
        
        setTimeout(() => {
          content.classList.add('expanded');
        }, 10);
        this.textContent = 'Show less';
      }
    });
  });
  
  // Make cards clickable
  processCards.forEach(card => {
    card.addEventListener('click', function(e) {
      // Ignore clicks on expand button
      if (e.target.classList.contains('crybug-expand-btn')) return;
      
      // Remove active class from all cards
      processCards.forEach(c => c.classList.remove('active'));
      
      // Add active class to clicked card
      this.classList.add('active');
      
      // Highlight step dot
      const stepNum = this.getAttribute('data-step');
      stepDots.forEach(dot => dot.classList.remove('active-highlight'));
      document.querySelector(`.crybug-step-dot[data-step="${stepNum}"]`).classList.add('active-highlight');
    });
  });
  
  // Demo button animation (click effect only, no floating)
  demoButton.addEventListener('click', function() {
    this.classList.add('scale-90');
    setTimeout(() => {
      this.classList.remove('scale-90');
      alert('Interactive CryBug demo would launch here!');
    }, 200);
  });
  
  
});
//======================================================== Process Section Animation ends ===========================================================================================



//======================================================== Grid  Animation starts ===========================================================================================

document.addEventListener('DOMContentLoaded', function() {
  // Handle feature cards animation
  const cards = document.querySelectorAll('.feature-card');
  
  // Apply a staggered delay to each card
  cards.forEach((card, index) => {
    card.style.transitionDelay = `${index * 100}ms`;
  });
  
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('fade-in');
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);
  
  cards.forEach(card => {
    observer.observe(card);
  });
  
  // Handle stats section animation
  const statElements = document.querySelectorAll('.fade-in');
  
  const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        statsObserver.unobserve(entry.target);
        
        // If this is a counter element, start counting
        const counter = entry.target.querySelector('.counter');
        if (counter) {
          animateCounter(counter);
        }
      }
    });
  }, observerOptions);
  
  statElements.forEach(el => {
    statsObserver.observe(el);
  });
  
  // Function to animate counters
  function animateCounter(counterElement) {
    const target = parseInt(counterElement.getAttribute('data-target'));
    const duration = 1500; // ms
    const stepTime = 20; // ms
    const totalSteps = duration / stepTime;
    const stepSize = target / totalSteps;
    let current = 0;
    
    const timer = setInterval(() => {
      current += stepSize;
      if (current >= target) {
        clearInterval(timer);
        counterElement.textContent = target;
      } else {
        counterElement.textContent = Math.round(current);
      }
    }, stepTime);
  }
  
  // Button hover effect
  const toolkitBtn = document.getElementById('try-toolkit-btn');
  
  toolkitBtn.addEventListener('mouseenter', () => {
    toolkitBtn.style.transform = 'translateY(-4px)';
  });
  
  toolkitBtn.addEventListener('mouseleave', () => {
    toolkitBtn.style.transform = 'translateY(0)';
  });
  
  // Glowing corner effect
  const cardElements = document.querySelectorAll(".card");
  
  window.addEventListener("mousemove", (ev) => {
    cardElements.forEach((e) => {
      const blob = e.querySelector(".blob");
      const fblob = e.querySelector(".fakeblob");
      
      if (blob && fblob) {
        const rec = fblob.getBoundingClientRect();
        
        blob.animate(
          [
            { 
              transform: `translate(${(ev.clientX - rec.left) - (rec.width / 2)}px, ${(ev.clientY - rec.top) - (rec.height / 2)}px)` 
            }
          ], 
          { 
            duration: 300, 
            fill: "forwards" 
          }
        );
        
        blob.style.opacity = "1";
      }
    });
  });
});


//======================================================== Grid  Animation ends ===========================================================================================


//======================================================== Process  Animation starts ===========================================================================================


document.addEventListener('DOMContentLoaded', function() {
  // Timeline animation on scroll
  const timelineItems = document.querySelectorAll('.timeline-item');
  
  // Animate items that are already in viewport on load
  animateItemsInViewport();
  
  // Handle scroll events
  window.addEventListener('scroll', function() {
    animateItemsInViewport();
  });
  
  function animateItemsInViewport() {
    timelineItems.forEach(item => {
      const position = item.getBoundingClientRect();
      
      // If item is in viewport
      if(position.top < window.innerHeight - 100) {
        item.classList.add('visible');
      }
    });
  }
  
  // Add pulse effect to CTA button
  const ctaButton = document.getElementById('get-started-btn');
  setTimeout(() => {
    ctaButton.classList.add('pulse-effect');
  }, 2000);
  
  // Track which item is currently expanded
  let currentExpandedItem = null;
  
  // Click event for timeline items
  timelineItems.forEach(item => {
    item.addEventListener('click', function(e) {
      e.stopPropagation(); // Prevent event from bubbling up
      const stepNumber = this.getAttribute('data-step');
      
      // Check if this item is already expanded
      const content = this.querySelector('.timeline-content');
      const isCurrentlyExpanded = content.classList.contains('bg-gray-800');
      
      // Remove highlight from all items
      timelineItems.forEach(i => i.querySelector('.timeline-content').classList.remove('bg-gray-800', 'rounded-lg', 'p-6'));
      
      // Only highlight if it wasn't already expanded
      if (!isCurrentlyExpanded) {
        content.classList.add('bg-gray-800', 'rounded-lg', 'p-6');
        currentExpandedItem = this;
      } else {
        currentExpandedItem = null;
      }
      
     
      console.log(`User clicked on step ${stepNumber}`);
    });
  });
  
 
  document.addEventListener('click', function(e) {
  
    if (currentExpandedItem && !e.target.closest('.timeline-item')) {
     
      currentExpandedItem.querySelector('.timeline-content').classList.remove('bg-gray-800', 'rounded-lg', 'p-6');
      currentExpandedItem = null;
    }
  });
});


//======================================================== Process  Animation ends ===========================================================================================


//======================================================== Project section starts ===========================================================================================




document.addEventListener('DOMContentLoaded', function() {

  
  // Filter Functionality
  const filterButtons = document.querySelectorAll('[id^="filter-"]');
  const projectCards = document.querySelectorAll('.project-card');
  
  filterButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Update active button styles
      filterButtons.forEach(btn => {
        btn.classList.remove('bg-indigo-600', 'text-white');
        btn.classList.add('bg-gray-700', 'text-gray-300');
      });
      this.classList.remove('bg-gray-700', 'text-gray-300');
      this.classList.add('bg-indigo-600', 'text-white');
      
      const category = this.id.replace('filter-', '');
      
      // Filter projects
      projectCards.forEach(card => {
        if (category === 'all' || card.dataset.category === category) {
          card.style.display = 'block';
          // Simple animation for reappearing cards
          card.style.opacity = '0';
          card.style.transform = 'translateY(20px)';
          setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
            card.style.transition = 'all 0.4s ease';
          }, 50);
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
  
 
  
  // Card hover effect enhancement
  projectCards.forEach(card => {
    card.addEventListener('mouseenter', function() {
      this.querySelectorAll('.tag').forEach((tag, index) => {
        setTimeout(() => {
          tag.style.transform = 'translateY(-4px)';
          setTimeout(() => {
            tag.style.transform = 'translateY(0)';
          }, 200);
        }, index * 50);
      });
    });
  });
});


//======================================================== Project section ends ===========================================================================================


//======================================================== gallery section starts ===========================================================================================

// Reveal animations on scroll using Intersection Observer
document.addEventListener('DOMContentLoaded', function() {
  // Handle scroll animations
  const revealElements = document.querySelectorAll('.slideIn');
  
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const delay = entry.target.getAttribute('data-delay') || 0;
        setTimeout(() => {
          entry.target.classList.add('active');
        }, parseFloat(delay) * 1000);
        revealObserver.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
  });
  
  revealElements.forEach(element => {
    revealObserver.observe(element);
  });
  
  // Add hover effects to team cards using vanilla JS
  const teamCards = document.querySelectorAll('.team-expert-card');
  
  teamCards.forEach(card => {
    const description = card.querySelector('p:last-child');
    
    card.addEventListener('mouseenter', () => {
      description.style.opacity = '1';
      description.style.transform = 'translateY(0)';
      description.style.transition = 'opacity 0.3s, transform 0.3s';
    });
    
    card.addEventListener('mouseleave', () => {
      description.style.opacity = '0';
      description.style.transform = 'translateY(10px)';
      description.style.transition = 'opacity 0.3s, transform 0.3s';
    });
  });
  
  // Create duplicate carousel items for infinite loop effect
  const carousel = document.querySelector('.team-carousel');
  const items = document.querySelectorAll('.team-expert-card');
  
  items.forEach(item => {
    const clone = item.cloneNode(true);
    carousel.appendChild(clone);
    
    // Add the same hover events to clones
    const cloneDescription = clone.querySelector('p:last-child');
    
    clone.addEventListener('mouseenter', () => {
      cloneDescription.style.opacity = '1';
      cloneDescription.style.transform = 'translateY(0)';
      cloneDescription.style.transition = 'opacity 0.3s, transform 0.3s';
    });
    
    clone.addEventListener('mouseleave', () => {
      cloneDescription.style.opacity = '0';
      cloneDescription.style.transform = 'translateY(10px)';
      cloneDescription.style.transition = 'opacity 0.3s, transform 0.3s';
    });
  });
});

//======================================================== gallery section ends ===========================================================================================



//========================================================Qna section stars ===========================================================================================



// This JavaScript file handles the toggle functionality for the FAQ section
document.addEventListener('DOMContentLoaded', function() {
  const faqToggles = document.querySelectorAll('.crybug-faq-toggle');
  
  faqToggles.forEach(toggle => {
    toggle.addEventListener('click', function() {
      // Get the parent FAQ item
      const faqItem = this.closest('.crybug-faq-item');
      
      // Get the content element to toggle
      const content = this.nextElementSibling;
      
      // Toggle active class on parent
      faqItem.classList.toggle('crybug-faq-active');
      
      // Toggle the arrow rotation
      const arrow = this.querySelector('i');
      if (faqItem.classList.contains('crybug-faq-active')) {
        arrow.style.transform = 'rotate(180deg)';
        // Calculate and set the max height of the content
        content.style.maxHeight = content.scrollHeight + 'px';
      } else {
        arrow.style.transform = 'rotate(0)';
        content.style.maxHeight = '0';
      }
    });
  });
  
  // Function to handle screen resize and adjust FAQ content heights
  function adjustFaqHeights() {
    const activeItems = document.querySelectorAll('.crybug-faq-active');
    
    activeItems.forEach(item => {
      const content = item.querySelector('.crybug-faq-content');
      if (content) {
        // Reset height to recalculate
        content.style.maxHeight = 'none';
        // Set new height based on content
        content.style.maxHeight = content.scrollHeight + 'px';
      }
    });
  }
  
  // Listen for window resize events
  window.addEventListener('resize', adjustFaqHeights);

});

//========================================================Qna section ends ===========================================================================================
