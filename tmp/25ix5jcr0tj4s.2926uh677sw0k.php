<div class="container">
    <div class="row mt-2">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 offset-sm-1 offset-md-2 offset-lg-3 p-3 bg-light rounded">
            <div class="mx-auto w-sm-75 w-lg-100 p-4 bg-white rounded">
                <h4 class="text-secondary">Reset Password:</h4>
                <hr>
                <small>
                    Password should be between 8 and 40 characters, contain at least 1 uppercase letter,
                    1 lowercase letter and 1 number.
                </small>

                <form action="#" method="post" class="mt-2 mb-2">
                    <div class="form-group">
                        <label for="oldPassword">Old Password</label>
                        <input type="password" class="form-control"
                               id="oldPassword" name="oldPassword">
                    </div>
                    <div class="form-group">
                        <label for="newPassword1">New Password</label>
                        <input type="password" class="form-control"
                               id="newPassword1" name="newPassword1">
                    </div>
                    <div class="form-group">
                        <label for="newPassword2">Repeat Password</label>
                        <input type="password" class="form-control"
                               id="newPassword2" name="newPassword2">
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
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