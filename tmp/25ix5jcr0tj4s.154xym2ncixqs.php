<div class="container">
    <div class="row mt-2">
        <div class="col-12 col-sm-10 col-md-8 offset-sm-1 offset-md-2 p-3 bg-light rounded">
            <div class="mx-auto w-sm-75 w-lg-100 p-4 bg-white rounded clearfix">
                <h2 class="text-secondary">Profile</h2>
                <hr>
                <div class="w-75 float-left">
                    <p>User ID: <?= ($GLOBALS['user']->getUserid()) ?></p>
                    <p>Username: <?= ($GLOBALS['user']->getUsername()) ?></p>
                    <p>Email: <?= ($GLOBALS['user']->getEmail()) ?></p>
                    <p>Privilege: <?= ($GLOBALS['user']->getPrivilege()) ?></p>
                </div>
                <div class="w-25 float-right">
                    <a href="<?= ($GLOBALS['user']->getUsername()) ?>/reset-password" class="btn btn-sm btn-outline-secondary mb-2"
                            id="btn-change-password">
                        Change Password
                    </a>
                    <br>
                    <a href="<?= ($GLOBALS['user']->getUsername()) ?>/change-email" class="btn btn-sm btn-outline-secondary"
                            id="btn-change-email">
                        Change Email
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
