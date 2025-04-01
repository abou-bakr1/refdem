-- Table des débats
CREATE TABLE IF NOT EXISTS debates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    argument_pour TEXT NOT NULL,
    argument_contre TEXT NOT NULL,
    theme VARCHAR(50) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES `groups`(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des votes
CREATE TABLE IF NOT EXISTS debate_votes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    debate_id INT NOT NULL,
    user_id INT NOT NULL,
    vote ENUM('pour', 'contre') NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (debate_id) REFERENCES debates(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_vote (debate_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
