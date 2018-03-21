<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <!-- Icon was made by Karneval-Lune @dA -->
    <img src="<?= ($BASE) ?>/assets/images/CCLogo.png" width="32" height="37" style='transform:scaleX(-1)' alt="CC-Logo">
    <a class="navbar-brand" href="<?= ($BASE) ?>/">CollegeCuisine</a>
    <img src="<?= ($BASE) ?>/assets/images/CCLogo.png" width="32" height="37" style="margin-left:-0.9rem" alt="CC-Logo">

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">

            <?php if ($authority >= 0): ?>
            
                <li class="nav-item">
                    <a class="nav-link" href="<?= ($BASE) ?>/recipe/new-recipe">New Recipe</a>
                </li>
            
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= ($BASE) ?>/recipe/<?= ($rand) ?>">Random Recipe</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <?php if (isset($_SESSION['user']) == true): ?>
                
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profile-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?= ($GLOBALS['user']->getUsername())."
" ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profile-dropdown">
                            <a class="dropdown-item" href="<?= ($BASE) ?>/profiles/<?= ($GLOBALS['user']->getUsername()) ?>">Profile</a>
                            <?php if ($authority >= 1): ?>
                                
                                    <a class="dropdown-item" href="<?= ($BASE) ?>/administration">Administration</a>
                                
                            <?php endif; ?>

                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?= ($BASE) ?>/login">Logout</a>
                        </div>
                    </li>
                
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= ($BASE) ?>/login">Login</a>
                    </li>
                
            <?php endif; ?>

        </ul>
    </div>
</nav>