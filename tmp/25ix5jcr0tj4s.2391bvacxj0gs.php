<div class="container">
    <div class="row mt-2">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 offset-sm-1 offset-md-2 offset-lg-3 p-3 bg-light rounded">
            <div class="mx-auto w-sm-75 w-lg-100 p-4 bg-white rounded">
                <h4 class="text-secondary">Reset Password:</h4>
                <hr>
                <form action="#" method="post" class="mt-2 mb-2">
                    <div class="form-group">
                        <label for="newEmail">New Email</label>
                        <input type="text" class="form-control"
                               id="newEmail" name="newEmail"
                               value="<?= ($_POST['newEmail']) ?>">
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                </form>
                <?php if (isset($invalid)): ?>
                    
                        <div class="alert alert-danger">
                            <p><?= ($invalid) ?></p>
                        </div>
                    
                <?php endif; ?>

            </div>

        </div>
    </div>

</div>