// Script for logout modal functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get the modal
    const logoutModal = document.getElementById('logoutModal');
    
    // Get the logout button that opens the modal
    const logoutBtn = document.querySelector('.logout a');
    
    // Get the close button elements
    const stayLoggedInBtn = document.querySelector('.btn-no');
    const confirmLogoutBtn = document.querySelector('.btn-yes');
    
    // When the user clicks the logout button, open the modal
    logoutBtn.addEventListener('click', function(e) {
        e.preventDefault();
        logoutModal.style.display = 'flex';
    });
    
    // When the user clicks on "No, Stay logged in", close the modal
    stayLoggedInBtn.addEventListener('click', function(e) {
        e.preventDefault();
        logoutModal.style.display = 'none';
    });
    
    // When the user clicks anywhere outside of the modal, close it
    window.addEventListener('click', function(event) {
        if (event.target == logoutModal) {
            logoutModal.style.display = 'none';
        }
    });
    
    // The "Yes, Log out" button should redirect to the logout processing script
    confirmLogoutBtn.addEventListener('click', function(e) {
        // This allows the link's href to work normally (redirect to logout.php?confirm=yes)
    });
});