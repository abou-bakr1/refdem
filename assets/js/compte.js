document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'upload de photo
    const photoInput = document.getElementById('photo-upload');
    const profileImage = document.getElementById('profile-image');

    photoInput.addEventListener('change', async function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Vérifier le type de fichier
        if (!file.type.startsWith('image/')) {
            alert('Veuillez sélectionner une image.');
            return;
        }

        // Vérifier la taille du fichier (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('L\'image ne doit pas dépasser 5MB.');
            return;
        }

        const formData = new FormData();
        formData.append('photo', file);

        try {
            const response = await fetch('../actions/update_photo.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
            if (data.success) {
                // Mettre à jour l'image affichée
                profileImage.src = data.photo_url + '?t=' + new Date().getTime();
            } else {
                alert(data.message || 'Une erreur est survenue lors du changement de photo.');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors du changement de photo.');
        }
    });

    // Validation du formulaire de mot de passe
    const passwordForm = document.querySelector('form[action="../actions/update_password.php"]');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
            }
        });
    }
});
