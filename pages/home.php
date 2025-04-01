<?php
require_once 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <!-- Mes groupes -->
        <div class="col-md-6 mb-4">
            <h2>Mes groupes</h2>
            <div id="my-groups" class="groups-container">
                <!-- Les groupes seront chargés ici dynamiquement -->
            </div>
        </div>

        <!-- Découvrir des groupes -->
        <div class="col-md-6 mb-4">
            <h2>Découvrir des groupes</h2>
            <div id="other-groups" class="groups-container">
                <!-- Les groupes à découvrir seront chargés ici -->
            </div>
        </div>
    </div>
</div>

<script>
// Charger mes groupes
function loadMyGroups() {
    fetch('actions/get_my_groups.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById('my-groups');
                container.innerHTML = data.groups.map(group => `
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">${group.name}</h5>
                            <p class="card-text">Créé par: ${group.creator_name}</p>
                            <p class="card-text"><small class="text-muted">${group.member_count} membres</small></p>
                            <button class="btn btn-primary" onclick="viewGroup(${group.id})">Consulter ce groupe</button>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// Charger les autres groupes
function loadOtherGroups() {
    fetch('actions/get_other_groups.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById('other-groups');
                container.innerHTML = data.groups.map(group => `
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">${group.name}</h5>
                            <p class="card-text">Créé par: ${group.creator_name}</p>
                            <p class="card-text"><small class="text-muted">${group.member_count} membres</small></p>
                            <button class="btn btn-success" onclick="joinGroup(${group.id})">Rejoindre</button>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// Fonction pour rejoindre un groupe
function joinGroup(groupId) {
    const formData = new FormData();
    formData.append('group_id', groupId);

    fetch('actions/join_group.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recharger les deux listes après avoir rejoint un groupe
            loadMyGroups();
            loadOtherGroups();
            alert(data.message);
        } else {
            alert(data.error);
        }
    })
    .catch(error => console.error('Erreur:', error));
}

// Fonction pour consulter un groupe
function viewGroup(groupId) {
    window.location.href = `pages/group.php?id=${groupId}`;
}

// Charger les groupes au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    loadMyGroups();
    loadOtherGroups();
});
</script>

<style>
.groups-container {
    max-height: 600px;
    overflow-y: auto;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

<?php
require_once 'includes/footer.php';
?>