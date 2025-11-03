document.addEventListener('DOMContentLoaded', async function() {
    await initialiserHeader();
    initialiserScrollHeader();
});

function initialiserScrollHeader() {
    const header = document.querySelector('header');
    if (!header) return;
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('header-scrolled');
        } else {
            header.classList.remove('header-scrolled');
        }
    });
}

async function initialiserHeader() {
    const header = document.querySelector('header');
    if (!header) return;
    
    const pageActuelle = determinerPageActuelle();
    const estConnecte = await verifierStatutConnexion();
    
    header.innerHTML = creerHeaderHTML(pageActuelle, estConnecte);
}

function determinerPageActuelle() {
    const path = window.location.pathname;
    const filename = path.split('/').pop();
    
    if (filename === 'index.html' || filename === '' || filename === 'index.php') {
        return 'accueil';
    } else if (filename === 'connexion.html') {
        return 'connexion';
    } else if (filename === 'inscription.html') {
        return 'inscription';
    } else if (filename === 'categories.php' || filename.includes('category')) {
        return 'categories';
    } else if (path.includes('/pages/')) {
        return 'pages';
    }
    
    return 'accueil';
}

function creerHeaderHTML(pageActuelle, estConnecte = false) {
    const estDansPages = pageActuelle !== 'accueil';
    const prefixe = estDansPages ? '../' : '';
    
    let headerHTML = `
        <div id="logo-div">
            <img src="${prefixe}assets/header_logo.png" alt="Logo Lend&Share" />
            <h1>Lend&Share</h1>
        </div>
    `;
    
    headerHTML += '<nav id="nav-principale">';
    
    switch (pageActuelle) {
        case 'accueil':
            headerHTML += `<a href="pages/categories.php" class="bouton-catalogue">Accès au Catalogue Complet</a>`;
            break;
            
        case 'categories':
        case 'pages':
            headerHTML += `<a href="${prefixe}index.html" class="bouton-catalogue">Accueil</a>`;
            break;
            
        case 'connexion':
            headerHTML += `
                <a href="${prefixe}index.html" class="bouton-catalogue">Accueil</a>
                <a href="categories.php" class="bouton-catalogue">Catalogue</a>
            `;
            break;
            
        case 'inscription':
            headerHTML += `
                <a href="${prefixe}index.html" class="bouton-catalogue">Accueil</a>
                <a href="categories.php" class="bouton-catalogue">Catalogue</a>
            `;
            break;
    }
    
    headerHTML += '</nav>';
    headerHTML += '<div id="boutons-auth">';
    
    if (estConnecte) {
        headerHTML += `
            <a href="${prefixe}pages/mon_compte.html" class="bouton-secondaire">Mon compte</a>
            <button onclick="deconnexion()" class="bouton-principal">Déconnexion</button>
        `;
    } else {
        switch (pageActuelle) {
            case 'accueil':
            case 'categories':
            case 'pages':
                headerHTML += `
                    <a href="${prefixe}pages/inscription.html" class="bouton-secondaire">Inscription</a>
                    <a href="${prefixe}pages/connexion.html" class="bouton-principal">Connexion</a>
                `;
                break;
                
            case 'connexion':
                headerHTML += `<a href="inscription.html" class="bouton-secondaire">Inscription</a>`;
                break;
                
            case 'inscription':
                headerHTML += `<a href="connexion.html" class="bouton-principal">Connexion</a>`;
                break;
        }
    }
    
    headerHTML += '</div>';
    
    return headerHTML;
}

async function verifierStatutConnexion() {
    try {
        const prefixe = window.location.pathname.includes('/pages/') ? '../' : '';
        const response = await fetch(`${prefixe}backend/api/api_verifier_session.php`);
        const data = await response.json();
        return data.connecte || false;
    } catch (error) {
        console.log('Impossible de vérifier le statut de connexion:', error);
        return false;
    }
}

async function deconnexion() {
    try {
        const prefixe = window.location.pathname.includes('/pages/') ? '../' : '';
        const response = await fetch(`${prefixe}backend/api/api_deconnexion.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.succes) {
            window.location.href = prefixe + 'index.html';
        } else {
            alert('Erreur lors de la déconnexion');
        }
    } catch (error) {
        console.error('Erreur lors de la déconnexion:', error);
        alert('Erreur lors de la déconnexion');
    }
}
