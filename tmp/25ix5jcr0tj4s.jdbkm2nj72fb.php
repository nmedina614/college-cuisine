<div class="container">
    <div class="row">
        <div class="col col-sm-6 offset-sm-3 bg-light p-2 mt-2 rounded">
            <div class="bg-white p-4 rounded">
                <h3 class="text-secondary">Verification</h3>
                <hr>
                <?php if ($result): ?>
                    
                        <div class="alert alert-success">
                            <p>Account verified!</p>
                        </div>
                    
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <p>Verification failed!</p>
                        </div>
                    
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>