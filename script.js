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
            "commissioned", "auctioneer", "appraisal", "online auction", "live auction", "my  
       
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
// Fullscreen modal open and close functions
function openModal(imageSrc, description) {
    // Get reference to the modal, image, and description elements
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const modalDesc = document.getElementById('modalDescription');

    modal.style.display = "flex"; // Display the modal with flex layout
    modalImg.src = imageSrc;      // Set the image source to provided imageSrc
    modalDesc.textContent = description; // Set description text to provided description
}

// Close the modal
function closeModal() {
    const modal = document.getElementById('imageModal'); // Get modal element
    modal.style.display = "none"; // Hide the modal
}

// Placeholder functions for buttons
function buyItem() {
    alert("Bid Price functionality is not yet implemented."); // Alert placeholder message
}

function addToCart() {
    alert("Add to Cart functionality is not yet implemented."); // Alert placeholder message
}

function moreInfo() {
    alert("More Information functionality is not yet implemented."); // Alert placeholder message
}





