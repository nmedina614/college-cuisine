<div class="container">
    <div <?php if (isset($errors)): ?>
            class="alert alert-danger" id="invalid-alert"
            <?php else: ?>class="alert alert-danger d-none" id="invalid-alert"
         <?php endif; ?>
    >

        <ul id="invalid-list">

            <?php if (isset($errors)): ?>
                
                    <?php foreach (($errors?:[]) as $error): ?>
                        <li><?= ($error) ?></li>
                    <?php endforeach; ?>
                
            <?php endif; ?>

        </ul>
    </div>
    <form method="post" action="new-recipe" enctype="multipart/form-data" id="recipeForm">

        <!-- form complex example -->
        <div class="form-row mt-4">
            <div class="col-sm-5 pb-3">
                <label for="recipeName">Recipe name</label>
                <input type="text" class="form-control" id="recipeName" placeholder="Name of Recipe" name="recipeName" value="<?= ($recipeName) ?>">
            </div>
            <div class="col-sm-3 pb-3">
                <label for="prepTime">Prep Time (in min)</label>
                <input type="text" class="form-control" id="prepTime" name="prepTime" value="<?= ($prepTime) ?>">
            </div>
            <div class="col-sm-4 pb-3">


                <label for="cookTime">Cook Time (in min)</label>
                <input type="text" class="form-control" id="cookTime" name="cookTime" value="<?= ($cookTime) ?>">

            </div>
            <div class="col-sm-6 pb-3">
                <label for="servings">Servings</label>
                <input type="text" class="form-control" id="servings" name="servs" value="<?= ($servings) ?>">
            </div>
            <div class="col-sm-6 pb-3">
                <label for="calories">Calories per serving</label>
                <input type="text" class="form-control" id="calories" name="cals" value="<?= ($calories) ?>">
            </div>

            <div class="col-md-12 pb-3">
                <label for="notes">Description</label>
                <textarea class="form-control" id="notes" name="description"><?= ($description) ?></textarea>
                <small class="text-info">
                    Add a small description about your recipe!
                </small>
            </div>

            <?php if (isset($ingreds)): ?>

                


                    <div class="col-md-6 pb-3">
                        <span>Ingredients <button type="button" class="btn btn-sm btn-light" id="btn-add-ingredient">Add <img class="icon-add" src="<?= ($BASE) ?>/assets/images/icons/plus.svg" alt=""></button></span>

                        <div id="ingredient-list">

                            <?php foreach (($ingreds?:[]) as $ingredient): ?>
                               <div class="input-group mb-3 ingredient-item">
                                    <input type="text" class="form-control" placeholder="Ex. 16oz ground beef" class="input-ingredient" name="ingreds[]" value="<?= ($ingredient) ?>">
                                    <button class="btn btn-danger btn-ingredient-delete" type="button"><img class="icon-delete" src="<?= ($BASE) ?>/assets/images/icons/close.svg" alt="Remove"></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                     </div>

                

                <?php else: ?>

                    <div class="col-md-6 pb-3">
                        <span>Ingredients <button type="button" class="btn btn-sm btn-light" id="btn-add-ingredient">Add <img class="icon-add" src="<?= ($BASE) ?>/assets/images/icons/plus.svg" alt=""></button></span>
                        <div id="ingredient-list">
                            <div class="input-group mb-3 ingredient-item">
                                <input type="text" class="form-control" placeholder="Ex. 16oz ground beef" class="input-ingredient" name="ingreds[]" value="<?= ($ingredient) ?>">
                                <button class="btn btn-danger btn-ingredient-delete" type="button"><img class="icon-delete" src="<?= ($BASE) ?>/assets/images/icons/close.svg" alt="Remove"></button>
                            </div>
                        </div>
                    </div>

                

            <?php endif; ?>


            <?php if (isset($directs)): ?>

                

                    <div class="col-md-6 pb-3">
                        <span>Direction <button type="button" class="btn btn-sm btn-light" id="btn-add-direct">Add <img class="icon-add" src="<?= ($BASE) ?>/assets/images/icons/plus.svg" alt=""></button></span>
                        <div id="direction-list">

                            <?php foreach (($directs?:[]) as $directions): ?>
                                <div class="input-group mb-3 direct-item">
                                    <input type="text" class="form-control" placeholder="Add Directions Here" class="input-directions" name="directs[]" value="<?= ($directions) ?>">
                                    <button class="btn btn-danger btn-direct-delete" type="button"><img class="icon-delete" src="<?= ($BASE) ?>/assets/images/icons/close.svg" alt="Remove"></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                

                <?php else: ?>

                    <div class="col-md-6 pb-3">
                        <span>Direction <button type="button" class="btn btn-sm btn-light" id="btn-add-direct">Add <img class="icon-add" src="<?= ($BASE) ?>/assets/images/icons/plus.svg" alt=""></button></span>
                        <div id="direction-list">
                            <div class="input-group mb-3 direct-item">
                                <input type="text" class="form-control" placeholder="Add Directions Here" class="input-directions" name="directs[]">
                                <button class="btn btn-danger btn-direct-delete" type="button"><img class="icon-delete" src="<?= ($BASE) ?>/assets/images/icons/close.svg" alt="Remove"></button>
                            </div>
                        </div>
                    </div>

                

            <?php endif; ?>

        </div>

            <div class="row">
                <div class="form-group col-md-12 pb-3">
                    <label for="fileToUpload">Choose a file to upload!</label>
                    <input class='form-control' type="file" name="fileToUpload" id="fileToUpload">
                </div>

                <div class="col-md-12 pb-3">
                    <button class="btn btn-primary mx-clear" type="submit" name="submit" id="submit">Submit</button>
                </div>
            </div>


    </form>
</div>