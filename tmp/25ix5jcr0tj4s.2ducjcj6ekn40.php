

<div class="container">
    <div class="row">
        <div class="col-10 col-md-8 col-lg-6 offset-1 offset-md-2 offset-md-3">
            <form class="form-signin" method="post" action="#">
                <h2 class="form-signin-heading">Please sign in</h2>
                <label for="username" class="sr-only">Username</label>
                <input type="text" id="username" name="username" class="form-control"
                       placeholder="Username" value="<?= ($_POST['username']) ?>" required autofocus>
                <label for="inputPassword" class="sr-only">Password</label>
                <input type="password" id="inputPassword" name="password" class="form-control"
                       placeholder="Password" required>
                <?php if ($invalid == true): ?>
                    
                        <div class="alert alert-danger" role="alert">
                            Invalid Username or Password!
                        </div>
                    
                <?php endif; ?>
                <!--<div class="checkbox">-->
                    <!--<label>-->
                        <!--<input type="checkbox" value="remember-me"> Remember me-->
                    <!--</label>-->
                <!--</div>-->
                <button class="btn btn-lg btn-block btn-primary" type="submit" name="submit">Sign in</button>
                <a href="<?= ($BASE) ?>/registration" class="btn btn-block btn-primary" type="button">Create Account</a>
            </form>
        </div>
    </div>
</div> <!-- /container -->

