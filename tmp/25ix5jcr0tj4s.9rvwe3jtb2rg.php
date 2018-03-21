<div class="container">
    <div class="row">
        <div class="col col-sm-6 offset-sm-3 bg-light p-2 mt-2 rounded">
            <div class="bg-white p-4 rounded">
                <h3 class="text-secondary">Register</h3>
                <hr>
                <form action="#" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username"
                               class="form-control" value="<?= ($_POST['username']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="password1">Password</label>
                        <input type="password" name="password1" id="password1"
                               class="form-control" value="<?= ($_POST['password1']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="password2">Repeat Password</label>
                        <input type="password" name="password2" id="password2"
                               class="form-control" value="<?= ($_POST['password2']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email"
                               class="form-control" value="<?= ($_POST['email']) ?>">
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submit" class="btn btn-block btn-primary" value="Submit">
                    </div>
                </form>
                <?php if (count($invalid) != 0): ?>
                    
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach (($invalid?:[]) as $value): ?>
                                    <li><?= ($value) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>