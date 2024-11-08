function validateForm() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmpassword').value;
    const errorMessage = document.getElementById('error-message');

    if (password !== confirmPassword) {
        errorMessage.textContent = "Passwords do not match!";
        errorMessage.style.display = "block"; // Show error message
        return false; // Prevent form submission
    }

    errorMessage.style.display = "none"; // Hide error message if passwords match

    // Call the API instead of submitting the form normally
    registerUser();
    return false; // Prevent the form from reloading the page
}

async function registerUser() {
    const formData = {
        firstname: document.getElementById('fname').value,
        lastname: document.getElementById('lname').value,
        username: document.getElementById('username').value,
        password: document.getElementById('password').value,
        access: document.getElementById('access').value
    };

    try {
        const response = await fetch('http://localhost/api/create-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (response.ok) {
            alert(result.message);
            document.getElementById("registrationForm").reset(); // Reset the form
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        document.getElementById('error-message').textContent = error.message;
    }
}

$(document).ready(function() {
    $('.select2').select2();
});

document.addEventListener('DOMContentLoaded', function () {
    const feedbackLinks = document.querySelectorAll('.edit-feedback-link');
    const feedbackTable = document.getElementById('feedback-table');
    const subcontent = document.getElementById('subcontent');

    feedbackLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent the default anchor behavior
        
            
            const url = this.getAttribute('href');

            // Hide the feedback table
            feedbackTable.style.display = 'none';

            // Fetch the content of the edit-feedback.php page
            fetch(url)
                .then(response => response.text())
                .then(data => {
                    // Insert the fetched content into the 'subcontent' div
                    subcontent.innerHTML = data;
                })
                .catch(error => console.error('Error loading the feedback edit page:', error));
        });
    });
});

