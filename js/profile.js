// Script for profile page functionality
document.addEventListener('DOMContentLoaded', function() {
    // Profile image preview functionality
    const profileImageInput = document.getElementById('profile_image');
    const profilePic = document.querySelector('.profile-pic');
    const photoError = document.getElementById('photoError');
    
    if (profileImageInput) {
        profileImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Validate file type
                const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                if (!validImageTypes.includes(file.type)) {
                    photoError.textContent = 'Please select a valid image file (JPG, PNG, or GIF)';
                    photoError.style.display = 'block';
                    profileImageInput.value = '';
                    return;
                }
                
                // Validate file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    photoError.textContent = 'Image size should be less than 2MB';
                    photoError.style.display = 'block';
                    profileImageInput.value = '';
                    return;
                }
                
                // Hide any previous errors
                photoError.style.display = 'none';
                
                // Create preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Clear previous content
                    while (profilePic.firstChild) {
                        profilePic.removeChild(profilePic.firstChild);
                    }
                    
                    // Create image element
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Profile Preview';
                    profilePic.appendChild(img);
                    
                    // Add back the change photo button
                    const changePhotoDiv = document.createElement('div');
                    changePhotoDiv.className = 'change-photo';
                    changePhotoDiv.textContent = 'Change Photo';
                    changePhotoDiv.onclick = function() {
                        document.getElementById('profile_image').click();
                    };
                    profilePic.appendChild(changePhotoDiv);
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Make fields editable on icon click (optional enhancement)
    const editIcons = document.querySelectorAll('.edit-icon');
    editIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const inputField = this.previousElementSibling;
            inputField.focus();
        });
    });
});