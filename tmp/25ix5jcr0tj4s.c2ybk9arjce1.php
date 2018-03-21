<div class="container bg-dark" data-source="<?= ($_SERVER['HTTP_HOST']) ?><?= ($BASE) ?>">
    <div class="row bg-light mt-2">
        <div class="col bg-white">
            <table id="example" class="table table-sm table-striped" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>UserID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Privilege</th>
                        <th>Selection</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach (($rows?:[]) as $row): ?>
                    <tr>
                        <td data-type="userid" data-value="<?= ($row['userid']) ?>"><?= ($row['userid']) ?></td>
                        <td data-type="username" data-value="<?= ($row['username']) ?>"><?= ($row['username']) ?></td>
                        <td data-type="email" data-value="<?= ($row['email']) ?>"><?= ($row['email']) ?></td>
                        <td data-type="privilege" data-value="<?= ($row['privilege']) ?>"><?= ($row['privilege']) ?></td>
                        <td>

                            <!-- Promote/Reinstate User -->
                            <?php if ($row['privilege'] != 'deactivated'): ?>
                                
                                    <?php if ($row['privilege'] == 'moderator'): ?>
                                        
                                            <button type="button" class="btn btn-sm btn-disabled"
                                                    data-toggle="tooltip" data-placement="top" title="Disabled">
                                                <img src="<?= ($BASE) ?>/assets/images/icons/verified.svg" alt="Disabled">
                                            </button>
                                        
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-dark btn-promote-user"
                                                    data-toggle="tooltip" data-placement="top" title="Promote User">
                                                <img src="<?= ($BASE) ?>/assets/images/icons/verified.svg" alt="promote">
                                            </button>
                                        
                                    <?php endif; ?>
                                
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-dark btn-reinstate-user"
                                            data-toggle="tooltip" data-placement="top" title="Reinstate User">
                                        <img src="<?= ($BASE) ?>/assets/images/icons/plus.svg" alt="Reinstate">
                                    </button>
                                
                            <?php endif; ?>


                            <!-- Reset password -->
                            <button type="button" class="btn btn-sm btn-dark btn-reset-password"
                                    data-toggle="tooltip" data-placement="top" title="Reset Password">
                                <img src="<?= ($BASE) ?>/assets/images/icons/lock-reset.svg" alt="password">
                            </button>

                            <!-- Ban/Delete User -->
                            <?php if ($row['privilege'] == 'deactivated'): ?>
                                
                                    <button type="button" class="btn btn-sm btn-danger btn-delete-user"
                                            data-toggle="tooltip" data-placement="top" title="Delete Account">
                                        <img src="<?= ($BASE) ?>/assets/images/icons/delete.svg" alt="delete">
                                    </button>
                                
                                <?php else: ?>
                                    <?php if ($row['privilege'] == 'basic'): ?>
                                        
                                            <button type="button" class="btn btn-sm btn-dark btn-ban-user"
                                                    data-toggle="tooltip" data-placement="top" title="Disable Account">
                                                <img src="<?= ($BASE) ?>/assets/images/icons/close.svg" alt="ban">
                                            </button>
                                        
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-dark btn-demote-user"
                                                    data-toggle="tooltip" data-placement="top" title="Demote Account">
                                                <img src="<?= ($BASE) ?>/assets/images/icons/minus.svg" alt="demote">
                                            </button>
                                        
                                    <?php endif; ?>
                                
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>