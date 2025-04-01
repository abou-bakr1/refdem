<?php
require_once("config/connexion.php");
Connexion::connect();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$request = isset($_SERVER['PATH_INFO']) ? explode('/', trim($_SERVER['PATH_INFO'], '/')) : [];

$resource = array_shift($request);
$id = array_shift($request);

switch ($resource) {
    case 'utilisateurs':
        require_once("modele/utilisateur.php");
        handleUtilisateurRequest($method, $id);
        break;
    case 'roles':
        require_once("modele/role.php");
        handleRoleRequest($method, $id);
        break;
    case 'groupes':
        require_once("modele/groupe.php");
        handleGroupeRequest($method, $id);
        break;
    case 'propositions':
        require_once("modele/proposition.php");
        handlePropositionRequest($method, $id);
        break;
    case 'commentaires':
        require_once("modele/commentaire.php");
        handleCommentaireRequest($method, $id);
        break;
    case 'votes':
        require_once("modele/vote.php");
        handleVoteRequest($method, $id);
        break;
    case 'décisions':
        require_once("modele/décision.php");
        handleDecisionRequest($method, $id);
        break;
    case 'membres_groupes':
        require_once("modele/membre_groupe.php");
        handleMembreGroupeRequest($method, $id);
        break;
    case 'votes_utilisateurs':
        require_once("modele/vote_utilisateur.php");
        handleVoteUtilisateurRequest($method, $id);
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Resource not found']);
        break;
}

function handleUtilisateurRequest($method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $utilisateur = Utilisateur::getUtilisateurById($id);
                echo json_encode($utilisateur);
            } else {
                $utilisateurs = Utilisateur::getAllutilisateurs();
                echo json_encode($utilisateurs);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['login'], $data['nomUtilisateur'], $data['prénomUtilisateur'], $data['adresseUtilisateur'], $data['mailUtilisateur'], $data['mdpUtilisateur'])) {
                $nomRole = isset($data['nomRole']) ? $data['nomRole'] : 'Membre';
                $utilisateur = new Utilisateur($data['login'], $data['nomUtilisateur'], $data['prénomUtilisateur'], $data['adresseUtilisateur'], $data['mailUtilisateur'], $data['mdpUtilisateur'], $nomRole);
                $utilisateur->save();
                echo json_encode(['message' => 'Utilisateur créé']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['nomUtilisateur'], $data['prénomUtilisateur'], $data['adresseUtilisateur'], $data['mailUtilisateur'], $data['mdpUtilisateur'], $data['nomRole'])) {
                $utilisateur = Utilisateur::getUtilisateurById($id);
                if ($utilisateur) {
                    $utilisateur->update($data);
                    echo json_encode(['message' => 'Utilisateur mis à jour']);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Utilisateur non trouvé']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'DELETE':
            $utilisateur = Utilisateur::getUtilisateurById($id);
            if ($utilisateur) {
                $utilisateur->delete();
                echo json_encode(['message' => 'Utilisateur supprimé']);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Utilisateur non trouvé']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
}

function handleRoleRequest($method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $role = Role::getRoleByNom($id);
                echo json_encode($role);
            } else {
                $roles = Role::getAllrole();
                echo json_encode($roles);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['nomRole'])) {
                $role = new Role($data['nomRole']);
                $role->save();
                echo json_encode(['message' => 'Role créé']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['nomRole'])) {
                $role = Role::getRoleByNom($id);
                if ($role) {
                    $role->update($data);
                    echo json_encode(['message' => 'Role mis à jour']);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Role non trouvé']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'DELETE':
            $role = Role::getRoleByNom($id);
            if ($role) {
                $role->delete();
                echo json_encode(['message' => 'Role supprimé']);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Role non trouvé']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
}

function handleGroupeRequest($method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $groupe = Groupe::getGroupeByNom($id);
                echo json_encode($groupe);
            } else {
                $groupes = Groupe::getAllgroupes();
                echo json_encode($groupes);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['nomGroupe'], $data['descriptionGroupe'], $data['imageGroupe'], $data['couleurGroupe'], $data['themeGroupe'], $data['admin'])) {
                $groupe = new Groupe($data['nomGroupe'], $data['descriptionGroupe'], $data['imageGroupe'], $data['couleurGroupe'], $data['themeGroupe'], $data['admin']);
                $groupe->save();
                echo json_encode(['message' => 'Groupe créé']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['descriptionGroupe'], $data['imageGroupe'], $data['couleurGroupe'], $data['themeGroupe'], $data['admin'])) {
                $groupe = Groupe::getGroupeByNom($id);
                if ($groupe) {
                    $groupe->update($data);
                    echo json_encode(['message' => 'Groupe mis à jour']);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Groupe non trouvé']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'DELETE':
            $groupe = Groupe::getGroupeByNom($id);
            if ($groupe) {
                $groupe->delete();
                echo json_encode(['message' => 'Groupe supprimé']);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Groupe non trouvé']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
}

function handlePropositionRequest($method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $proposition = Proposition::getPropositionById($id);
                echo json_encode($proposition);
            } else {
                $propositions = Proposition::getAllPropositions();
                echo json_encode($propositions);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['titre'], $data['description'], $data['etiquette'], $data['thème'], $data['proposeur'], $data['nomGroupe'])) {
                $proposition = new Proposition($data['titre'], $data['description'], $data['etiquette'], $data['thème'], $data['proposeur'], $data['nomGroupe']);
                $proposition->save();
                echo json_encode(['message' => 'Proposition créée']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['titre'], $data['description'], $data['etiquette'], $data['thème'], $data['proposeur'], $data['nomGroupe'])) {
                $proposition = Proposition::getPropositionById($id);
                if ($proposition) {
                    $proposition->update($data);
                    echo json_encode(['message' => 'Proposition mise à jour']);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Proposition non trouvée']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'DELETE':
            $proposition = Proposition::getPropositionById($id);
            if ($proposition) {
                $proposition->delete();
                echo json_encode(['message' => 'Proposition supprimée']);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Proposition non trouvée']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
}

function handleCommentaireRequest($method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $commentaire = Commentaire::getCommentaireById($id);
                echo json_encode($commentaire);
            } else {
                $commentaires = Commentaire::getAllCommentaires();
                echo json_encode($commentaires);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['contenu'], $data['date_Commentaire'], $data['id_proposition'], $data['login'])) {
                $commentaire = new Commentaire($data['contenu'], $data['date_Commentaire'], $data['id_proposition'], $data['login']);
                $commentaire->save();
                echo json_encode(['message' => 'Commentaire créé']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['contenu'], $data['date_Commentaire'], $data['id_proposition'], $data['login'])) {
                $commentaire = Commentaire::getCommentaireById($id);
                if ($commentaire) {
                    $commentaire->update($data);
                    echo json_encode(['message' => 'Commentaire mis à jour']);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Commentaire non trouvé']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'DELETE':
            $commentaire = Commentaire::getCommentaireById($id);
            if ($commentaire) {
                $commentaire->delete();
                echo json_encode(['message' => 'Commentaire supprimé']);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Commentaire non trouvé']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
}

function handleVoteRequest($method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $vote = Vote::getVoteById($id);
                echo json_encode($vote);
            } else {
                $votes = Vote::getAllVotes();
                echo json_encode($votes);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['typeVote'], $data['dateDebut'], $data['dateFin'], $data['id_proposition'])) {
                $vote = new Vote($data['typeVote'], $data['dateDebut'], $data['dateFin'], $data['id_proposition']);
                $vote->save();
                echo json_encode(['message' => 'Vote créé']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['typeVote'], $data['dateDebut'], $data['dateFin'], $data['id_proposition'])) {
                $vote = Vote::getVoteById($id);
                if ($vote) {
                    $vote->update($data);
                    echo json_encode(['message' => 'Vote mis à jour']);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Vote non trouvé']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'DELETE':
            $vote = Vote::getVoteById($id);
            if ($vote) {
                $vote->delete();
                echo json_encode(['message' => 'Vote supprimé']);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Vote non trouvé']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
}

function handleDecisionRequest($method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $decision = Décision::getDecisionById($id);
                echo json_encode($decision);
            } else {
                $decisions = Décision::getAllDecisions();
                echo json_encode($decisions);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['budget'], $data['résultatDécision'], $data['id_vote'], $data['décideur'])) {
                $decision = new Décision($data['budget'], $data['résultatDécision'], $data['id_vote'], $data['décideur']);
                $decision->save();
                echo json_encode(['message' => 'Décision créée']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['budget'], $data['résultatDécision'], $data['id_vote'], $data['décideur'])) {
                $decision = Décision::getDecisionById($id);
                if ($decision) {
                    $decision->update($data);
                    echo json_encode(['message' => 'Décision mise à jour']);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Décision non trouvée']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'DELETE':
            $decision = Décision::getDecisionById($id);
            if ($decision) {
                $decision->delete();
                echo json_encode(['message' => 'Décision supprimée']);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Décision non trouvée']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
}

function handleMembreGroupeRequest($method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                list($login, $nomGroupe) = explode(',', $id);
                $membreGroupe = Membre_Groupe::getMembreGroupe($login, $nomGroupe);
                echo json_encode($membreGroupe);
            } else {
                $membresGroupes = Membre_Groupe::getAllMembresGroupes();
                echo json_encode($membresGroupes);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['login'], $data['nomGroupe'])) {
                $membreGroupe = new Membre_Groupe($data['login'], $data['nomGroupe']);
                $membreGroupe->save();
                echo json_encode(['message' => 'Membre ajouté au groupe']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['nomGroupe'])) {
                list($login, $nomGroupe) = explode(',', $id);
                $membreGroupe = Membre_Groupe::getMembreGroupe($login, $nomGroupe);
                if ($membreGroupe) {
                    $membreGroupe->update($data);
                    echo json_encode(['message' => 'Membre mis à jour']);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Membre non trouvé']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'DELETE':
            list($login, $nomGroupe) = explode(',', $id);
            $membreGroupe = Membre_Groupe::getMembreGroupe($login, $nomGroupe);
            if ($membreGroupe) {
                $membreGroupe->delete();
                echo json_encode(['message' => 'Membre supprimé du groupe']);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Membre non trouvé']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
}

function handleVoteUtilisateurRequest($method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                list($login, $id_vote) = explode(',', $id);
                $voteUtilisateur = Vote_Utilisateur::getVoteUtilisateur($login, $id_vote);
                echo json_encode($voteUtilisateur);
            } else {
                $votesUtilisateurs = Vote_Utilisateur::getAllVotesUtilisateurs();
                echo json_encode($votesUtilisateurs);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['login'], $data['id_vote'], $data['résultat'], $data['date_Vote'])) {
                $voteUtilisateur = new Vote_Utilisateur($data['login'], $data['id_vote'], $data['résultat'], $data['date_Vote']);
                $voteUtilisateur->save();
                echo json_encode(['message' => 'Vote utilisateur créé']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['résultat'], $data['date_Vote'])) {
                list($login, $id_vote) = explode(',', $id);
                $voteUtilisateur = Vote_Utilisateur::getVoteUtilisateur($login, $id_vote);
                if ($voteUtilisateur) {
                    $voteUtilisateur->update($data);
                    echo json_encode(['message' => 'Vote utilisateur mis à jour']);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Vote utilisateur non trouvé']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
            }
            break;
        case 'DELETE':
            list($login, $id_vote) = explode(',', $id);
            $voteUtilisateur = Vote_Utilisateur::getVoteUtilisateur($login, $id_vote);
            if ($voteUtilisateur) {
                $voteUtilisateur->delete();
                echo json_encode(['message' => 'Vote utilisateur supprimé']);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Vote utilisateur non trouvé']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
}
?>