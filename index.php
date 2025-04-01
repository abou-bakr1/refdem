<?php
session_start();
require_once 'config/connexion.php';
require_once 'includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: pages/connexion.php');
    exit();
}

$page = 'index';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Refdem</title>
    <link rel="stylesheet" href="assets/css/header-footer.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <style>
        .toggle-view {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .toggle-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            background: #f0f0f0;
            color: #666;
        }

        .toggle-btn.active {
            background: #6a62a3;
            color: white;
        }

        .group-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .group-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .group-header h3 {
            margin: 0;
            color: #333;
        }

        .member-count {
            font-size: 14px;
            color: #666;
        }

        .group-description {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .group-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-info img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

        .admin-info span {
            font-size: 14px;
            color: #666;
        }

        .group-actions {
            display: flex;
            gap: 10px;
        }

        .primary-btn {
            background: #6a62a3;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .secondary-btn {
            background: #f0f0f0;
            color: #333;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .danger-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .primary-btn:hover {
            background: #554e8c;
        }

        .secondary-btn:hover {
            background: #e0e0e0;
        }

        .danger-btn:hover {
            background: #c82333;
        }

        .no-group {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="container">
        <div class="slider-wrapper">
            <input type="radio" name="slider" id="slide-1" checked>
            <input type="radio" name="slider" id="slide-2">
            <input type="radio" name="slider" id="slide-3">

            <div class="slider">
                <div class="slide">
                    <img src="assets/img/Bercy.jpg" alt="Bercy">
                    <div class="text">
                        <h1>Loi spéciale : l'explication de texte des ministres Antoine Armand et Laurent Saint-Martin.</h1>
                        <h3>Le projet de loi spéciale qui doit permettre à l'État, aux services publics, et au pays, de continuer à fonctionner en attendant le vote d'un projet de loi de finances en bonne et due forme.</h3>
                    </div>
                </div>
                <div class="slide">
                    <img src="assets/img/trump.jpg" alt="Trump">
                    <div class="text">
                        <h1>Donald Trump va sonner la cloche à Wall Street qui bat des records.</h1>
                        <h3>Sous l'effet des promesses du président élu, de la baisse des taux d'intérêt orchestrée par la Fed et de l'explosion du secteur de la tech, les trois principaux indices de la Bourse de New York se sont envolés mercredi.</h3>
                    </div>
                </div>
                <div class="slide">
                    <img src="assets/img/content1.jpg" alt="Content">
                    <div class="text">
                        <h1>Assistants des eurodéputés FN.</h1>
                        <h3>Le procès d'un système organisé au service des finances du parti frontiste, de nombreuses figures du parti d'extrême droite défileront à partir de lundi.</h3>
                    </div>
                </div>
            </div>

            <div class="slider-nav">
                <label for="slide-1"></label>
                <label for="slide-2"></label>
                <label for="slide-3"></label>
            </div>
        </div>
    </section>

    <section class="propos">
        <h1 class="propos-titre">Refdem ? C'est quoi ?</h1>
        <div>
            <div class="bloc1">
                <h1>Notre Mission</h1>
                <h2>Promouvoir le débat citoyen, analyser l'actualité et encourager la participation démocratique.</h2>
            </div>
            <div class="imaage">
                <img src="assets/img/debatsGens.jpg" alt="Débats">
            </div>
            <div class="bloc3">
                <h1>Pourquoi nous choisir ?</h1>
                <h2>Une communauté engagée, des actualités vérifiées, et un espace sécurisé pour partager ses opinions.</h2>
            </div>
        </div>
    </section>

    <section class="forum">
        <div class="forum-title">
            <h1>Ta voix compte.</h1>
            <h1>Fais-toi entendre.</h1>
        </div>
        <form action="pages/forum.php">
            <input type="submit" value="PARTICIPE À NOTRE FORUM !">
        </form>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadMyGroup();
            loadOtherGroups();
        });

        function toggleView(view) {
            const myGroupView = document.getElementById('myGroupView');
            const discoverView = document.getElementById('discoverView');
            const myGroupBtn = document.getElementById('myGroupBtn');
            const discoverBtn = document.getElementById('discoverBtn');

            if (view === 'myGroup') {
                myGroupView.style.display = 'block';
                discoverView.style.display = 'none';
                myGroupBtn.classList.add('active');
                discoverBtn.classList.remove('active');
            } else {
                myGroupView.style.display = 'none';
                discoverView.style.display = 'block';
                myGroupBtn.classList.remove('active');
                discoverBtn.classList.add('active');
            }
        }

        function loadMyGroup() {
            fetch('actions/get_my_groups.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const container = document.getElementById('myGroupView');
                        let html = '';
                        
                        if (data.groups.length > 0) {
                            const group = data.groups[0]; // On prend le premier groupe car un utilisateur ne peut avoir qu'un seul groupe
                            html = `
                                <div class="group-card">
                                    <div class="group-header">
                                        <h3>${group.nom}</h3>
                                        <span class="member-count">
                                            <i class="fas fa-users"></i> ${group.member_count} membres
                                        </span>
                                    </div>
                                    <p class="group-description">${group.description}</p>
                                    <div class="group-footer">
                                        <div class="admin-info">
                                            <img src="${group.admin_photo}" alt="Admin">
                                            <span>${group.admin_name}</span>
                                        </div>
                                        <div class="group-actions">
                                            ${group.is_admin ? `
                                                <button onclick="window.location.href='forum.php?group=${group.id}'" class="secondary-btn">
                                                    <i class="fas fa-eye"></i> Parcourir le forum
                                                </button>
                                            ` : `
                                                <button onclick="quitterGroupe(${group.id})" class="danger-btn">
                                                    <i class="fas fa-sign-out-alt"></i> Quitter
                                                </button>
                                                <button onclick="window.location.href='forum.php?group=${group.id}'" class="primary-btn">
                                                    <i class="fas fa-eye"></i> Consulter
                                                </button>
                                            `}
                                        </div>
                                    </div>
                                </div>
                            `;
                        } else {
                            html = `
                                <div class="no-group">
                                    <p>Vous n'avez pas encore de groupe. Découvrez les groupes disponibles !</p>
                                </div>
                            `;
                        }
                        
                        container.innerHTML = html;
                    }
                });
        }

        function loadOtherGroups() {
            fetch('actions/get_other_groups.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const container = document.getElementById('discoverView');
                        let html = '';
                        
                        if (data.groups.length > 0) {
                            data.groups.forEach(group => {
                                html += `
                                    <div class="group-card">
                                        <div class="group-header">
                                            <h3>${group.nom}</h3>
                                            <span class="member-count">
                                                <i class="fas fa-users"></i> ${group.member_count} membres
                                            </span>
                                        </div>
                                        <p class="group-description">${group.description}</p>
                                        <div class="group-footer">
                                            <div class="admin-info">
                                                <img src="${group.admin_photo}" alt="Admin">
                                                <span>${group.admin_name}</span>
                                            </div>
                                            <div class="group-actions">
                                                <button onclick="rejoindreGroupe(${group.id})" class="primary-btn">
                                                    <i class="fas fa-plus"></i> Rejoindre
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            html = `
                                <div class="no-group">
                                    <p>Aucun groupe disponible pour le moment.</p>
                                </div>
                            `;
                        }
                        
                        container.innerHTML = html;
                    }
                });
        }

        function rejoindreGroupe(groupId) {
            fetch('actions/join_group.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ groupId: groupId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadMyGroup();
                    loadOtherGroups();
                    toggleView('myGroup');
                } else {
                    alert(data.message);
                }
            });
        }

        function quitterGroupe(groupId) {
            if (confirm('Êtes-vous sûr de vouloir quitter ce groupe ?')) {
                fetch('actions/leave_group.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ groupId: groupId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadMyGroup();
                        loadOtherGroups();
                    } else {
                        alert(data.message);
                    }
                });
            }
        }
    </script>
    
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/index.js"></script>
</body>
</html>
