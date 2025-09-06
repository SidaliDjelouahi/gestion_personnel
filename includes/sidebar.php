<!-- Sidebar -->
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">

            <!-- Menu Paramètres -->
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" 
                   data-bs-toggle="collapse" 
                   href="#submenuParametres" 
                   role="button" 
                   aria-expanded="false" 
                   aria-controls="submenuParametres">
                    <span><i class="fa fa-cog"></i> Paramètres</span>
                    <i class="fa fa-chevron-down small"></i>
                </a>
                <div class="collapse" id="submenuParametres">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ROOT_URL ?>/parametres/services.php">
                                <i class="fa fa-briefcase"></i> Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ROOT_URL ?>/parametres/grades.php">
                                <i class="fa fa-layer-group"></i> Grades
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ROOT_URL ?>/parametres/categories.php">
                                <i class="fa fa-list"></i> Catégories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ROOT_URL ?>/parametres/echelons.php">
                                <i class="fa fa-chart-line"></i> Échelons
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ROOT_URL ?>/parametres/primes.php">
                                <i class="fa fa-coins"></i> Primes
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Menu Personnel -->
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center"
                   data-bs-toggle="collapse"
                   href="#submenuPersonnel"
                   role="button"
                   aria-expanded="false"
                   aria-controls="submenuPersonnel">
                    <span><i class="fa fa-users"></i> Personnel</span>
                    <i class="fa fa-chevron-down small"></i>
                </a>
                <div class="collapse" id="submenuPersonnel">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ROOT_URL ?>/personnel/liste.php">
                                <i class="fa fa-list"></i> Liste du personnel
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ROOT_URL ?>/personnel/detail.php">
                                <i class="fa fa-id-card"></i> Détails
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Menu Carrière -->
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center"
                   data-bs-toggle="collapse"
                   href="#submenuCarriere"
                   role="button"
                   aria-expanded="false"
                   aria-controls="submenuCarriere">
                    <span><i class="fa fa-chart-line"></i> Carrière</span>
                    <i class="fa fa-chevron-down small"></i>
                </a>
                <div class="collapse" id="submenuCarriere">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ROOT_URL ?>/carriere/avancement.php">
                                <i class="fa fa-arrow-up"></i> Avancement
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ROOT_URL ?>/carriere/promotions.php">
                                <i class="fa fa-medal"></i> Promotions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ROOT_URL ?>/carriere/mutations.php">
                                <i class="fa fa-exchange-alt"></i> Mutations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ROOT_URL ?>/carriere/regles.php">
                                <i class="fa fa-list-alt"></i> Règles carrière
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Absences -->
            <li class="nav-item">
                <a class="nav-link" href="<?= ROOT_URL ?>/absences/index.php">
                    <i class="fa fa-calendar-xmark"></i> Absences
                </a>
            </li>

        </ul>
    </div>
</nav>

<!-- Contenu principal -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-3">
