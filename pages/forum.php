<?php
session_start();
require_once '../config/connexion.php';
$page_title = "Forum - ReForum";
$page = 'forum';
$base_path = '../';

// Vérifier si l'utilisateur a déjà un groupe et récupérer ses informations
$hasGroup = false;
$groupInfo = null;
$userInfo = null;

if (isset($_SESSION['user_id'])) {
    $pdo = Connexion::pdo();
    
    // Récupérer les informations du groupe
    $stmt = $pdo->prepare("SELECT g.* FROM `groups` g WHERE g.admin_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $groupInfo = $stmt->fetch();
    $hasGroup = ($groupInfo !== false);

    // Récupérer les informations de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userInfo = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum - Refdem</title>
    <link rel="stylesheet" href="../assets/css/header-footer.css">
    <link rel="stylesheet" href="../assets/css/forum.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .toggle-group-view {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .toggle-group-view button {
            background: none;
            border: none;
            font-size: 20px;
            color: #6a62a3;
            cursor: pointer;
            padding: 5px;
            margin-right: 10px;
            transition: transform 0.3s ease;
        }

        .toggle-group-view button:hover {
            transform: rotate(90deg);
        }

        .toggle-group-view span {
            font-size: 18px;
            color: #333;
            font-weight: 500;
        }

        .discover-groups {
            padding: 20px;
        }

        .group-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .group-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .group-icon {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background: #f0f0f0;
        }

        .group-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .group-title {
            font-size: 18px;
            color: #333;
            margin: 0;
            font-weight: 500;
        }

        .group-admin {
            font-size: 14px;
            color: #666;
            margin: 0;
        }

        .group-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .member-count {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #6a62a3;
            font-size: 14px;
        }

        .member-count i {
            font-size: 16px;
        }

        .join-btn {
            background: white;
            color: #6a62a3;
            border: 2px solid #6a62a3;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .join-btn:hover {
            background: #6a62a3;
            color: white;
        }

        .consult-btn {
            background: white;
            color: #6a62a3;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .consult-btn:hover {
            text-decoration: underline;
        }

        .leave-btn {
            background: white;
            color: #dc3545;
            border: 2px solid #dc3545;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .leave-btn:hover {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <?php include_once '../includes/header.php';?>
    
    <div class="forum-header">
        <h1>Forum de discussion</h1>
    </div>

    <div class="container">
        <div class="main-content">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-profile">
                    <div class="profile-image">
                        <img src="<?php echo $userInfo['photo_profil'] ? '../uploads/profile/' . $userInfo['photo_profil'] : '../assets/images/default-profile.png'; ?>" alt="Photo de profil">
                    </div>
                    <div class="profile-info">
                        <h3><?php echo htmlspecialchars($userInfo['prenom'] . ' ' . $userInfo['nom']); ?></h3>
                    </div>
                </div>
                <div class="forum-actions">
                    <?php if ($hasGroup): ?>
                        <button class="action-btn view-group" onclick="viewMyGroup(<?php echo $groupInfo['id']; ?>)">
                            <i class="fas fa-users"></i> Consulter mon groupe
                        </button>
                        <button class="action-btn delete-group" onclick="deleteGroup(<?php echo $groupInfo['id']; ?>)">
                            <i class="fas fa-trash"></i> Supprimer mon groupe
                        </button>
                    <?php else: ?>
                        <button class="action-btn" onclick="openCreateModal()">
                            <i class="fas fa-plus"></i> Ajouter un groupe
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Liste des groupes ici -->
            <div class="groups-list">
                <!-- Le contenu de la liste des groupes sera ajouté ici -->
            </div>
        </div>

        <!-- Section droite toujours visible -->
        <div class="group-details">
            <div class="toggle-group-view">
                <button id="toggleGroupView" onclick="toggleGroupView()">
                    <i class="fas fa-times"></i>
                </button>
                <span id="viewTitle">Mon Groupe</span>
            </div>

            <div id="myGroupView">
                <div class="group-actions">
                    <button class="group-action-btn" onclick="showDiscussionForm()">
                        <i class="fas fa-comments"></i>
                        Créer une discussion
                    </button>
                    <button class="group-action-btn" onclick="showDebateForm()">
                        <i class="fas fa-gavel"></i>
                        Créer un débat
                    </button>
                    <button class="group-action-btn">
                        <i class="fas fa-users-cog"></i>
                        Gérer les membres
                    </button>
                </div>
            </div>

            <div id="discoverGroupsView" style="display: none;">
                <div class="discover-groups">
                    <!-- Les autres groupes seront chargés ici -->
                </div>
            </div>

            <div id="discussionForm" style="display: none;">
                <h3>Prêt à discuter !</h3>
                <input type="text" id="sujet" placeholder="Sujet" required>
                <textarea id="commentaire" placeholder="Commentaire" required></textarea>
                <select id="theme" required>
                    <option value="">Sélectionner un thème</option>
                    <option value="politique">Politique</option>
                    <option value="societe">Société</option>
                    <option value="economie">Économie</option>
                    <option value="environnement">Environnement</option>
                    <option value="culture">Culture</option>
                </select>
                <button onclick="publierDiscussion(<?php echo $groupInfo['id']; ?>)">Confirmer</button>
            </div>
            <div id="debateForm" style="display: none;">
                <h3>Prêt à débattre !</h3>
                <input type="text" id="debatSujet" placeholder="Sujet" required>
                <textarea id="debatDescription" placeholder="Description" required></textarea>
                <textarea id="debatArgumentPour" placeholder="Argument pour" required></textarea>
                <textarea id="debatArgumentContre" placeholder="Argument contre" required></textarea>
                <select id="debatTheme" required>
                    <option value="">Sélectionner un thème</option>
                    <option value="politique">Politique</option>
                    <option value="societe">Société</option>
                    <option value="economie">Économie</option>
                    <option value="environnement">Environnement</option>
                    <option value="culture">Culture</option>
                </select>
                <button onclick="publierDebat(<?php echo $groupInfo['id']; ?>)">Confirmer</button>
            </div>
            <div id="publications"></div>
            <div id="debates"></div>
        </div>

        <!-- Modal pour créer un groupe -->
        <div id="createGroupModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Créer un groupe</h2>
                    <span class="close" onclick="closeCreateModal()">&times;</span>
                </div>
                <div class="identification">
                    <div id="message"></div>
                    <form id="createGroupForm">
                        <input type="text" id="groupName" name="groupName" placeholder="NOM DU GROUPE" required>
                        <input type="submit" value="CRÉER LE GROUPE" class="action-btn">
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de confirmation de suppression -->
        <div id="deleteConfirmModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Confirmer la suppression</h2>
                    <span class="close" onclick="closeDeleteModal()">&times;</span>
                </div>
                <div class="identification">
                    <p>Êtes-vous sûr de vouloir supprimer votre groupe ? Cette action est irréversible.</p>
                    <div class="modal-actions">
                        <button class="action-btn cancel" onclick="closeDeleteModal()">Annuler</button>
                        <button class="action-btn delete" onclick="confirmDelete()">Supprimer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer.php';?>

    <script>
    let groupToDelete = null;

    document.getElementById('createGroupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('../actions/create_group.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const messageDiv = document.getElementById('message');
            messageDiv.innerHTML = `<div class="alert ${data.success ? 'alert-success' : 'alert-danger'}">${data.message}</div>`;
            
            if (data.success) {
                // Fermer la modal après 2 secondes et recharger la page
                setTimeout(() => {
                    closeCreateModal();
                    location.reload();
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('message').innerHTML = `
                <div class="alert alert-danger">Une erreur est survenue lors de la création du groupe</div>
            `;
        });
    });

    function openCreateModal() {
        document.getElementById('createGroupModal').style.display = 'block';
        document.getElementById('message').innerHTML = '';
        document.getElementById('createGroupForm').reset();
    }

    function closeCreateModal() {
        document.getElementById('createGroupModal').style.display = 'none';
        document.getElementById('message').innerHTML = '';
        document.getElementById('createGroupForm').reset();
    }

    function viewMyGroup(groupId) {
        const groupTitle = document.querySelector('.group-details-content h2');
        const groupInfo = document.querySelector('.group-info');
        
        groupTitle.textContent = 'Mon Groupe';
        groupInfo.innerHTML = `
            <div class="group-actions">
                <button class="group-action-btn" onclick="showDiscussionForm()">
                    <i class="fas fa-comments"></i>
                    Créer une discussion
                </button>
                <button class="group-action-btn" onclick="showDebateForm()">
                    <i class="fas fa-gavel"></i>
                    Créer un débat
                </button>
                <button class="group-action-btn">
                    <i class="fas fa-users-cog"></i>
                    Gérer les membres
                </button>
            </div>
            <div id="discussionForm" style="display: none;">
                <h3>Prêt à discuter !</h3>
                <input type="text" id="sujet" placeholder="Sujet" required>
                <textarea id="commentaire" placeholder="Commentaire" required></textarea>
                <select id="theme" required>
                    <option value="">Sélectionner un thème</option>
                    <option value="politique">Politique</option>
                    <option value="societe">Société</option>
                    <option value="economie">Économie</option>
                    <option value="environnement">Environnement</option>
                    <option value="culture">Culture</option>
                </select>
                <button onclick="publierDiscussion(${groupId})">Confirmer</button>
            </div>
            <div id="debateForm" style="display: none;">
                <h3>Prêt à débattre !</h3>
                <input type="text" id="debatSujet" placeholder="Sujet" required>
                <textarea id="debatDescription" placeholder="Description" required></textarea>
                <textarea id="debatArgumentPour" placeholder="Argument pour" required></textarea>
                <textarea id="debatArgumentContre" placeholder="Argument contre" required></textarea>
                <select id="debatTheme" required>
                    <option value="">Sélectionner un thème</option>
                    <option value="politique">Politique</option>
                    <option value="societe">Société</option>
                    <option value="economie">Économie</option>
                    <option value="environnement">Environnement</option>
                    <option value="culture">Culture</option>
                </select>
                <button onclick="publierDebat(${groupId})">Confirmer</button>
            </div>
            <div id="publications"></div>
            <div id="debates"></div>
        `;

        // Charger les discussions existantes
        loadDiscussions(groupId);
        loadDebates(groupId);
    }

    function loadDiscussions(groupId) {
        console.log('Chargement des discussions pour le groupe:', groupId);
        fetch(`../actions/get_discussions.php?groupId=${groupId}`)
            .then(response => response.json())
            .then(discussions => {
                console.log('Discussions reçues:', discussions);
                const publicationsDiv = document.getElementById('publications');
                let discussionsHtml = '';
                discussions.forEach(discussion => {
                    console.log('Discussion courante:', discussion);
                    console.log('Est propriétaire:', discussion.is_owner);
                    discussionsHtml += `
                        <div class="publication" id="discussion-${discussion.id}">
                            <div class="publication-header">
                                <img src="${discussion.user_photo}" alt="Photo de profil">
                                <div class="publication-header-info">
                                    <h4>${discussion.user_name}</h4>
                                    <span>${discussion.created_at}</span>
                                </div>
                                ${discussion.is_owner ? `
                                    <button onclick="supprimerDiscussion(${discussion.id}, ${groupId})" class="delete-discussion">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                ` : ''}
                            </div>
                            <div class="publication-content">
                                <h3>${discussion.sujet}</h3>
                                <p>${discussion.commentaire}</p>
                                <span class="publication-theme">${discussion.theme}</span>
                            </div>
                            <div class="comments-section" id="comments-${discussion.id}">
                                <div class="comments-list"></div>
                                <div class="comment-form">
                                    <textarea placeholder="Écrivez un commentaire..." class="comment-input"></textarea>
                                    <button onclick="publierCommentaire(${discussion.id}, ${groupId})" class="comment-submit">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                console.log('HTML généré:', discussionsHtml);
                publicationsDiv.innerHTML = discussionsHtml;
                
                // Charger les commentaires pour chaque discussion
                discussions.forEach(discussion => {
                    loadComments(discussion.id);
                });
            })
            .catch(error => {
                console.error('Erreur lors du chargement des discussions:', error);
            });
    }

    function loadComments(discussionId) {
        fetch(`../actions/get_comments.php?discussionId=${discussionId}`)
            .then(response => response.json())
            .then(comments => {
                const commentsListDiv = document.querySelector(`#comments-${discussionId} .comments-list`);
                let commentsHtml = '';
                comments.forEach(comment => {
                    commentsHtml += createCommentHtml(comment, discussionId);
                });
                commentsListDiv.innerHTML = commentsHtml;
            });
    }

    function createCommentHtml(comment, discussionId) {
        return `
            <div class="comment" id="comment-${comment.id}">
                <div class="comment-header">
                    <img src="${comment.user_photo}" alt="Photo de profil">
                    <div class="comment-info">
                        <h4>${comment.user_name}</h4>
                        <span>${comment.created_at}</span>
                    </div>
                    ${comment.is_owner ? `
                        <button onclick="supprimerCommentaire(${comment.id}, ${discussionId})" class="delete-comment">
                            <i class="fas fa-trash"></i>
                        </button>
                    ` : ''}
                </div>
                <div class="comment-content">
                    <p>${comment.content}</p>
                </div>
            </div>
        `;
    }

    function publierDiscussion(groupId) {
        const sujet = document.getElementById('sujet').value;
        const commentaire = document.getElementById('commentaire').value;
        const theme = document.getElementById('theme').value;

        if (!sujet || !commentaire || !theme) {
            alert('Veuillez remplir tous les champs');
            return;
        }

        const formData = new FormData();
        formData.append('groupId', groupId);
        formData.append('sujet', sujet);
        formData.append('commentaire', commentaire);
        formData.append('theme', theme);

        fetch('../actions/create_discussion.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recharger les discussions
                loadDiscussions(groupId);
                // Cacher le formulaire et réinitialiser les champs
                document.getElementById('discussionForm').style.display = 'none';
                document.getElementById('sujet').value = '';
                document.getElementById('commentaire').value = '';
                document.getElementById('theme').value = '';
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la création de la discussion');
        });
    }

    function supprimerDiscussion(discussionId, groupId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette discussion ?')) {
            fetch('../actions/delete_discussion.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ discussionId: discussionId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recharger les discussions après la suppression
                    loadDiscussions(groupId);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erreur lors de la suppression de la discussion:', error);
                alert('Une erreur est survenue lors de la suppression de la discussion');
            });
        }
    }

    function showDiscussionForm() {
        const form = document.getElementById('discussionForm');
        if (form) {
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    }

    function deleteGroup(groupId) {
        groupToDelete = groupId;
        document.getElementById('deleteConfirmModal').style.display = 'block';
    }

    function closeDeleteModal() {
        document.getElementById('deleteConfirmModal').style.display = 'none';
        groupToDelete = null;
    }

    function confirmDelete() {
        if (groupToDelete) {
            fetch('../actions/delete_group.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ groupId: groupToDelete })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erreur lors de la suppression du groupe:', error);
                alert('Une erreur est survenue lors de la suppression du groupe');
            });
        }
    }

    function publierCommentaire(discussionId, groupId) {
        const commentInput = document.querySelector(`#comments-${discussionId} .comment-input`);
        const content = commentInput.value.trim();
        
        if (!content) {
            alert('Le commentaire ne peut pas être vide');
            return;
        }
        
        const formData = new FormData();
        formData.append('discussionId', discussionId);
        formData.append('content', content);
        
        fetch('../actions/create_comment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Ajouter le nouveau commentaire à la liste
                const commentsListDiv = document.querySelector(`#comments-${discussionId} .comments-list`);
                commentsListDiv.insertAdjacentHTML('beforeend', createCommentHtml(data.comment, discussionId));
                
                // Réinitialiser le champ de commentaire
                commentInput.value = '';
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la création du commentaire:', error);
            alert('Une erreur est survenue lors de la création du commentaire');
        });
    }

    function supprimerCommentaire(commentId, discussionId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) {
            fetch('../actions/delete_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ commentId: commentId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Supprimer le commentaire du DOM
                    document.getElementById(`comment-${commentId}`).remove();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erreur lors de la suppression du commentaire:', error);
                alert('Une erreur est survenue lors de la suppression du commentaire');
            });
        }
    }

    function showDebateForm() {
        const form = document.getElementById('debateForm');
        if (form) {
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    }

    function loadDebates(groupId) {
        fetch(`../actions/get_debates.php?groupId=${groupId}`)
            .then(response => response.json())
            .then(debates => {
                const debatesDiv = document.getElementById('debates');
                let debatesHtml = '';
                debates.forEach(debate => {
                    const totalVotes = debate.votes_pour + debate.votes_contre;
                    const pourcentagePour = totalVotes > 0 ? Math.round((debate.votes_pour / totalVotes) * 100) : 0;
                    const pourcentageContre = totalVotes > 0 ? Math.round((debate.votes_contre / totalVotes) * 100) : 0;
                    
                    debatesHtml += `
                        <div class="debate" id="debate-${debate.id}">
                            <div class="debate-header">
                                <img src="${debate.user_photo}" alt="Photo de profil">
                                <div class="debate-header-info">
                                    <h4>${debate.user_name}</h4>
                                    <span>${debate.created_at}</span>
                                </div>
                                ${debate.is_owner ? `
                                    <button onclick="supprimerDebat(${debate.id}, ${groupId})" class="delete-debate">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                ` : ''}
                            </div>
                            <div class="debate-content">
                                <h3>${debate.sujet}</h3>
                                <p class="debate-description">${debate.description}</p>
                                <div class="debate-arguments">
                                    <div class="argument pour">
                                        <h4>Arguments Pour</h4>
                                        <p>${debate.argument_pour}</p>
                                        <button onclick="voter(${debate.id}, 'pour', ${groupId})" 
                                            class="vote-btn pour ${debate.user_vote === 'pour' ? 'active' : ''}">
                                            <i class="fas fa-thumbs-up"></i>
                                            <span>${debate.votes_pour}</span>
                                        </button>
                                    </div>
                                    <div class="argument contre">
                                        <h4>Arguments Contre</h4>
                                        <p>${debate.argument_contre}</p>
                                        <button onclick="voter(${debate.id}, 'contre', ${groupId})" 
                                            class="vote-btn contre ${debate.user_vote === 'contre' ? 'active' : ''}">
                                            <i class="fas fa-thumbs-down"></i>
                                            <span>${debate.votes_contre}</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="vote-progress">
                                    <div class="progress-bar">
                                        <div class="progress-pour" style="width: ${pourcentagePour}%"></div>
                                        <div class="progress-contre" style="width: ${pourcentageContre}%"></div>
                                    </div>
                                    <div class="vote-stats">
                                        <span class="pour">${pourcentagePour}%</span>
                                        <span class="contre">${pourcentageContre}%</span>
                                    </div>
                                </div>
                                <span class="debate-theme">${debate.theme}</span>
                            </div>
                        </div>
                    `;
                });
                debatesDiv.innerHTML = debatesHtml;
            });
    }

    function publierDebat(groupId) {
        const sujet = document.getElementById('debatSujet').value;
        const description = document.getElementById('debatDescription').value;
        const argumentPour = document.getElementById('debatArgumentPour').value;
        const argumentContre = document.getElementById('debatArgumentContre').value;
        const theme = document.getElementById('debatTheme').value;

        if (!sujet || !description || !argumentPour || !argumentContre || !theme) {
            alert('Tous les champs sont obligatoires');
            return;
        }

        const formData = new FormData();
        formData.append('groupId', groupId);
        formData.append('sujet', sujet);
        formData.append('description', description);
        formData.append('argumentPour', argumentPour);
        formData.append('argumentContre', argumentContre);
        formData.append('theme', theme);

        fetch('../actions/create_debate.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadDebates(groupId);
                document.getElementById('debateForm').style.display = 'none';
                document.getElementById('debatSujet').value = '';
                document.getElementById('debatDescription').value = '';
                document.getElementById('debatArgumentPour').value = '';
                document.getElementById('debatArgumentContre').value = '';
                document.getElementById('debatTheme').value = '';
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la création du débat');
        });
    }

    function voter(debateId, vote, groupId) {
        fetch('../actions/vote_debate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ debateId: debateId, vote: vote })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadDebates(groupId);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors du vote');
        });
    }

    function supprimerDebat(debateId, groupId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce débat ?')) {
            fetch('../actions/delete_debate.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ debateId: debateId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadDebates(groupId);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de la suppression du débat');
            });
        }
    }

    let isMyGroupView = true;

    function toggleGroupView() {
        const myGroupView = document.getElementById('myGroupView');
        const discoverGroupsView = document.getElementById('discoverGroupsView');
        const viewTitle = document.getElementById('viewTitle');
        const toggleBtn = document.getElementById('toggleGroupView');
        const publications = document.getElementById('publications');
        const debates = document.getElementById('debates');
        const leftSection = document.querySelector('.left-section');

        isMyGroupView = !isMyGroupView;

        if (isMyGroupView) {
            myGroupView.style.display = 'block';
            discoverGroupsView.style.display = 'none';
            viewTitle.textContent = 'Mon Groupe';
            toggleBtn.style.transform = 'rotate(0deg)';
            publications.style.display = 'block';
            debates.style.display = 'block';
            leftSection.style.display = 'block';
        } else {
            myGroupView.style.display = 'none';
            discoverGroupsView.style.display = 'block';
            viewTitle.textContent = 'Découvrir les groupes';
            toggleBtn.style.transform = 'rotate(45deg)';
            publications.style.display = 'none';
            debates.style.display = 'none';
            leftSection.style.display = 'none';
            loadOtherGroups();
        }
    }

    function loadOtherGroups() {
        fetch('../actions/get_other_groups.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const container = document.getElementById('discoverGroupsView');
                    let html = '';
                    
                    data.groups.forEach(group => {
                        html += `
                            <div class="group-card">
                                <div class="group-left">
                                    <div class="group-icon">
                                        <img src="../assets/img/groupe.png" alt="Groupe">
                                    </div>
                                    <div class="group-info">
                                        <h3 class="group-title">${group.nom}</h3>
                                        <p class="group-admin">${group.admin_name}</p>
                                    </div>
                                </div>
                                <div class="group-right">
                                    <div class="member-count">
                                        <i class="fas fa-users"></i>
                                        ${group.member_count} membres
                                    </div>
                                    <button onclick="rejoindreGroupe(${group.id})" class="join-btn">
                                        <i class="fas fa-plus"></i> Rejoindre
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    
                    if (data.groups.length === 0) {
                        html = '<p class="no-groups">Aucun groupe disponible pour le moment.</p>';
                    }
                    
                    container.innerHTML = html;
                }
            });
    }

    function rejoindreGroupe(groupId) {
        fetch('../actions/join_group.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ groupId: groupId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toggleGroupView(); // Retour à la vue "Mon Groupe"
                window.location.reload(); // Recharger la page pour afficher le nouveau groupe
            } else {
                alert(data.message);
            }
        });
    }

    // Fermer les modals si on clique en dehors
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            closeCreateModal();
            closeDeleteModal();
        }
    }
    </script>
</body>
</html>