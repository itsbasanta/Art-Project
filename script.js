// Sample sentences for search predictions 
const sentencesList = [
    "Art", "Painting", "Sculpture", "Auction", "Gallery", "Exhibition", "Bidding", "Canvas", "Artisan", "Collector", "Masterpiece",
     "Contemporary", "Abstract", "Installation", "Curator", "Medium", "Palette", "Brushstroke", "Visual", "Composition", "Canvas",
      "Framing", "Authenticity", "Provenance", "Bidder", "Reserve", "Value", "Estimate", "Certificate", "Technique", "Style", "Artistic", 
      "Expression", "Creativity", "Market", "Transaction", "Lot", "Condition", "Hammer Price", "Opening Bid", "Final Bid", "Catalog",
       "Highlight", "Sales", "Gallery Owner", "Art Fair", "Collectible", "Auction House", "Private Sale", "Vernissage", "Art Critique",
       "Vincent van Gogh", "Pablo Picasso", "Leonardo da Vinci", "Claude Monet", "Jackson Pollock", "Frida Kahlo", "Georgia O'Keeffe", "Sushmita Maharjan", 
       "Basanta Sapkota" , "Shifu", "abstract", "modern", "sculpture", "oil painting", "canvas", "portrait", "landscape", "Impressionism", "expressionism", "surrealism", "contemporary", "vintage", "classic", "acrylic", "watercolor", "pastel", "etching", "sketch",
        "drawings", "lithograph", "limited edition", "original", "antique", "rare", "photography", "fine art", "bronze", "marble", "wood", "ceramics", "glass art",
         "Japanese", "Chinese", "African art", "installation", "urban art", "pop art", "graffiti", "decorative", "figurative", "minimalism", "baroque", "rococo",
          "Renaissance", "gothic", "folk art", "cubism", "art deco", "Impressionist", "Fauvism", "romanticism", "neoclassical", "street art", "contemporary realism",
           "calligraphy", "print", "handcrafted", "surreal", "mixed media", "mural", "symbolism", "museum quality", "signed", "artist proof", "giclee", "illustration",
            "poster", "vibrant", "monochrome", "conceptual", "collage", "decor", "garden sculpture", "wall art", "art glass", "ceramic sculpture", "stone sculpture",
            "metal sculpture", "landscape painting", "still life", "historical", "mid-century", "hand-painted", "limited print", "exhibition", "auction house", 
            "investment", "provenance", "gallery", "restoration", "framed", "mounting", "past master", "emerging artist", "art sale", "bidding", "private sale", 
            "commissioned", "auctioneer", "appraisal", "online auction", "live auction"       
       ];
// DOM elements
// Get reference to the search input field
const searchInput = document.getElementById('search');
// Get reference to the container for displaying search predictions
const predictionsContainer = document.getElementById('predictions');
// Get reference to the "not found" message element
const notFoundMessage = document.getElementById('not-found-message');

// Show predictions based on user input
function showPredictions() {
    // Retrieve user input, converted to lowercase for case-insensitive search
    const userInput = searchInput.value.toLowerCase();
    predictionsContainer.innerHTML = ''; // Clear any previous predictions
    predictionsContainer.style.display = 'none'; // Hide predictions initially

    if (userInput) { // Check if there's input to search for
        // Filter sentences that contain the user input
        const filteredSentences = sentencesList.filter(sentence =>
            sentence.toLowerCase().includes(userInput)
        );

        // Create a div element for each matching prediction
        filteredSentences.forEach(sentence => {
            const item = document.createElement('div'); // Create a new div element
            item.classList.add('prediction-item'); // Add a CSS class for styling
            item.textContent = sentence; // Set the text content to the prediction

            // Populate search input with prediction when clicked, and hide the list
            item.onclick = () => {
                searchInput.value = sentence; // Set input field to selected prediction
                predictionsContainer.style.display = 'none'; // Hide predictions container
            };
            predictionsContainer.appendChild(item); // Add prediction item to container
        });

        // Display the predictions container if there are any matches
        if (filteredSentences.length > 0) {
            predictionsContainer.style.display = 'block';
        }
    }
}

// Perform search action
function performSearch() {
    // Retrieve the current search input value, trimming any extra whitespace
    const searchValue = searchInput.value.trim();

    // Check if the search value matches any sentence in the list
    if (sentencesList.some(sentence => sentence.toLowerCase() === searchValue.toLowerCase())) {
        alert("Found: " + searchValue); // Display found content in an alert
    } else {
        showNotFoundMessage(); // Show "not found" message if no match is found
    }
}

// Show 'not found' message
function showNotFoundMessage() {
    notFoundMessage.style.display = 'block'; // Display the "not found" message
    setTimeout(() => {
        notFoundMessage.style.display = 'none'; // Hide message after 3 seconds
    }, 3000);
}

// Add event listener to trigger search when Enter key is pressed in the search box
searchInput.addEventListener('keypress', (event) => {
    if (event.key === 'Enter') { // Check if Enter key was pressed
        performSearch(); // Perform search action
    }
});


// Slide Show
// Get elements
const fullscreenModal = document.getElementById("fullscreenModal");
const fullscreenImage = document.getElementById("fullscreenImage");
const modalTitle = document.getElementById("modalTitle");
const closeBtn = document.getElementById("closeBtn");

// Function to open the modal
function openModal(imageSrc, title) {
    fullscreenImage.src = imageSrc;          // Set the image source
    modalTitle.textContent = title;           // Set the title
    fullscreenModal.style.display = "flex";   // Display the modal

    fullscreenImage.style.setProperty('--zoom-image', `url(${imageSrc})`);
}

// Function to close the modal
function closeModal() {
    fullscreenModal.style.display = "none";   // Hide the modal
}

// Event listener for the close button
closeBtn.addEventListener("click", closeModal);

// Optional: Close modal when clicking outside the modal content
fullscreenModal.addEventListener("click", (e) => {
    if (e.target === fullscreenModal) {
        closeModal();
    }
});





// game //
const canvas = document.getElementById("canvas");
const ctx = canvas.getContext("2d");
canvas.width = 800;
canvas.height = 500;

let drawing = false;
let color = document.getElementById("colorPicker").value;

// Update color based on color picker input
document.getElementById("colorPicker").addEventListener("input", (e) => {
  color = e.target.value;
});

// Draw on canvas
canvas.addEventListener("mousedown", () => (drawing = true));
canvas.addEventListener("mouseup", () => {
  drawing = false;
  ctx.beginPath(); // Start a new path
});
canvas.addEventListener("mousemove", draw);

function draw(e) {
  if (!drawing) return;
  ctx.lineWidth = 5;
  ctx.lineCap = "round";
  ctx.strokeStyle = color;

  ctx.lineTo(e.clientX - canvas.offsetLeft, e.clientY - canvas.offsetTop);
  ctx.stroke();
  ctx.beginPath();
  ctx.moveTo(e.clientX - canvas.offsetLeft, e.clientY - canvas.offsetTop);
}

// Clear canvas
document.getElementById("clearButton").addEventListener("click", () => {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
});

// Download canvas as image
document.getElementById("downloadButton").addEventListener("click", () => {
  const link = document.createElement("a");
  link.download = "quick_drawing.png";
  link.href = canvas.toDataURL("image/png");
  link.click();
});









// login form //
// Show Login or Signup Form
function openLoginForm() {
    document.getElementById('formTitle').innerText = 'Log In';
    document.getElementById('submitBtn').innerText = 'Log In';
    document.getElementById('loginSignupPopup').style.display = 'flex';
    document.getElementById('loginSignupForm').reset(); // Reset form fields
  }
  
  function openSignupForm() {
    document.getElementById('formTitle').innerText = 'Sign Up';
    document.getElementById('submitBtn').innerText = 'Sign Up';
    document.getElementById('loginSignupPopup').style.display = 'flex';
    document.getElementById('loginSignupForm').reset(); // Reset form fields
  }
  
  function closePopup(event) {
    const popup = document.getElementById('loginSignupPopup');
    
    // Check if the close button is clicked
    if (event.target.classList.contains('close-button') || 
        (event.target === popup && event.target.classList.contains('popup'))) {
        popup.style.display = 'none';
    }
}

// Optional: Close the popup when clicking outside of it
window.onclick = function(event) {
    const popup = document.getElementById('loginSignupPopup');
    if (event.target === popup) {
        closePopup(event);
    }
};

  
  // Toggle Password Visibility
  function togglePassword() {
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirmPassword');
    
    if (passwordField.type === 'password') {
      passwordField.type = 'text';
      confirmPasswordField.type = 'text';
    } else {
      passwordField.type = 'password';
      confirmPasswordField.type = 'password';
    }
  }
    
  // Handle form submission (Login/Signup)
  document.getElementById('loginSignupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Handle login/signup logic here
    alert('Form submitted');
    closePopup();  // Close popup after submission
  });

  

  // artists //
  /// Information about the artists
const artistsInfo = [
    {
      name: "PABLO PICASSO (1881-1973)",
      image: "Image/artist1.jpg",
      bio: "PABLO PICASSO is known for their abstract expressionism style, exploring emotions through vivid colors and dynamic shapes."
    },
    {
      name: "GIOTTO DI BONDONE (c.1267-1337)",
      image: "Image/artist2.jpg",
      bio: "GIOTTO DI BONDONE was an Italian painter and architect from the region of Tuscany, credited with the invention of modern painting."
    },
    {
      name: "LEONARDO DA VINCI (1452-1519)",
      image: "Image/artist3.jpg",
      bio: "LEONARDO DA VINCI was an Italian Renaissance polymath, renowned for his paintings, inventions, and scientific studies."
    },
    {
      name: "PAUL CÉZANNE (1839-1906)",
      image: "Image/artist4.jpg",
      bio: "PAUL CÉZANNE was a French post-impressionist painter who greatly influenced modern art with his unique brushstrokes and bold color palettes."
    },
    {
      name: "REMBRANDT VAN RIJN (1606-1669)",
      image: "Image/artist5.jpg",
      bio: "REMBRANDT was a Dutch master known for his contributions to realism, chiaroscuro, and his emotional depth in portraiture."
    },
    {
      name: "DIEGO VELÁZQUEZ (1599-1660)",
      image: "Image/artist6.jpg",
      bio: "DIEGO VELÁZQUEZ was a Spanish painter renowned for his depiction of royal life and masterful use of light."
    },
    {
      name: "WASSILY KANDINSKY (1866-1944)",
      image: "Image/artist7.jpg",
      bio: "WASSILY KANDINSKY was a Russian painter and art theorist, pioneering abstract art through vibrant colors and geometric forms."
    },
    {
      name: "CLAUDE MONET (1840-1926)",
      image: "Image/artist8.jpg",
      bio: "CLAUDE MONET was a French Impressionist artist, celebrated for his innovative use of color and light, especially in outdoor landscapes."
    },
    {
      name: "MICHELANGELO MERISI DA CARAVAGGIO (1571-1610)",
      image: "Image/artist9.jpg",
      bio: "CARAVAGGIO was an Italian Baroque artist whose realistic and intense depictions of religious subjects revolutionized art."
    },
    {
      name: "JAN VAN EYCK (1390-1441)",
      image: "Image/artist10.jpg",
      bio: "JAN VAN EYCK was a Flemish painter who pioneered oil painting techniques and achieved remarkable detail and color in his works."
    },
    {
      name: "JOSEPH-MALLORD WILLIAM TURNER (1775-1851)",
      image: "Image/artist11.jpg",
      bio: "J.M.W. TURNER was an English Romantic painter, famed for his expressive landscapes and seascapes, capturing light and color like never before."
    },
    {
      name: "ALBRECHT DÜRER (1471-1528)",
      image: "Image/artist12.jpg",
      bio: "ALBRECHT DÜRER was a German Renaissance artist, known for his woodcuts, engravings, and high-quality depictions of nature and the human form."
    },
    {
      name: "MICHELANGELO BUONARROTI (1475-1564)",
      image: "Image/artist13.jpg",
      bio: "MICHELANGELO BUONARROTI was an Italian Renaissance sculptor, painter, architect, and poet, celebrated for his works in the Sistine Chapel."
    },
    {
      name: "FRANCISCO DE GOYA (1746-1828)",
      image: "Image/artist14.jpg",
      bio: "FRANCISCO DE GOYA was a Spanish Romantic painter known for his dark and evocative images of war and human suffering."
    },
    {
      name: "VINCENT VAN GOGH (1853-1890)",
      image: "Image/artist15.jpg",
      bio: "VINCENT VAN GOGH was a Dutch Post-Impressionist painter whose emotional use of color and brushwork became highly influential in modern art."
    }
  ];
  
  // Function to open the popup with artist information
  function openPopup(index) {
    const popup = document.getElementById("popup");
    const artistInfo = artistsInfo[index];
    document.getElementById("popup-img").src = artistInfo.image;
    document.getElementById("artist-info").textContent = artistInfo.bio;
    popup.style.display = "flex";
  }
  
  // Function to close the popup
  function closePopup() {
    document.getElementById("popup").style.display = "none";
  }
  
  // go to top //
  // Function to scroll to the top of the page
function goToTop() {
  window.scrollTo({
    top: 0,
    behavior: "smooth"
  });
}
