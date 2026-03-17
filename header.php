<header>
    <div class="header-wrapper">
        <h1>Gestion d'Événements</h1>
        <nav>
            <a href="index2.php"><i class="fas fa-home"></i> Accueil</a>
            <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <a href="admin.php"><i class="fas fa-cog"></i> Admin</a>
            <?php endif; ?>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </nav>
    </div>
</header>

<style>
header {
    background-color: #2c3e50;
    color: white;
    padding: 1rem 2rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.header-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

header h1 {
    font-size: 1.5rem;
    font-weight: 600;
    color: white;
    margin: 0;
}

header nav {
    display: flex;
    gap: 1rem;
    align-items: center;
}

header nav a {
    color: #ecf0f1;
    text-decoration: none;
    font-size: 0.95rem;
    padding: 0.4rem 0.9rem;
    border-radius: 5px;
    transition: background 0.3s;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

header nav a:hover {
    background-color: #3498db;
    color: white;
}
</style>