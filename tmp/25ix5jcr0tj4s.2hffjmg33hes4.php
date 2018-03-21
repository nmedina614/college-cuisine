<div class="container" data-source="<?= ($_SERVER['HTTP_HOST']) ?><?= ($BASE) ?>">

    <div <?php if (isset($error)): ?>
         class="alert alert-danger alert-custom" id="invalid-alert"
         <?php endif; ?>
        <?php if (isset($success)): ?>
            class="alert alert-success alert-custom" id="success-alert"
        <?php endif; ?>
    >

    <ul id="invalid-list">

        <?php if (isset($error)): ?>
            
                <li><?= ($error) ?></li>
            
        <?php endif; ?>
        <?php if (isset($success)): ?>
            
                <li><?= ($success) ?></li>
            
        <?php endif; ?>

    </ul>
</div>

    <!-- Portfolio Item Heading -->
    <h1 class="my-4"><?= ($recipe[name]) ?></h1>


    <form method="post" action="<?= ($BASE) ?>/recipe/<?= ($recipe['recipeid']) ?>">

        <div class="container">
            <div class="row">
                <div class="col">
                </div>
                <div class="col">
                </div>
                <div class="col text-center">
                    <button class="btn btn-primary btn-recipe" type="submit" name="like">Like!</button>
                    <button class="btn btn-primary btn-recipe" type="submit" name="dislike">Dislike!</button>
                </div>
            </div>
        </div>

    </form>

    <div class="container">
        <div class="row">
            <div class="col">
            </div>
            <div class="col">
            </div>
            <div class="col text-center">
                <button class="btn btn-primary btn-delete-recipe btn-recipe"name="delete">Delete!</button>
            </div>
        </div>
    </div>


    <!-- Portfolio Item Row -->
    <div class="row">


        <div class="col-md-8">
            <img class="img-fluid" src="../<?= ($image) ?>" alt="testing">
        </div>

        <div class="col-md-4">
            <h3 class="my-3">Details</h3>
            <p><?= ($recipe['descript']) ?></p>
            <!-- Small Details added w/ SQL -->
            <ul>
                <li>Servings: <?= ($recipe[servings]) ?></li>
                <li>Calories per Serving: <?= ($recipe[cal]) ?></li>
                <li>Prep Time: <?= ($recipe[prepTime]) ?></li>
                <li>Cook Time: <?= ($recipe[cookTime]) ?></li>
                <li>Total Time: <?= ($recipe[prepTime] + $recipe[cookTime]) ?></li>
            </ul>
            <!--Ingredients SQL -->
            <h3 class="my-3">Ingredients</h3>
            <ul>
                <?php foreach (($ingredients?:[]) as $ingredient): ?>

                    <li><?= ($ingredient) ?></li>

                <?php endforeach; ?>
            </ul>

        </div>

    </div>
    <!-- /.row -->

    <!-- Related Projects Row -->
    <h3 class="my-4"></h3>

    <div class="row">
        <ol>

        <?php foreach (($directions?:[]) as $direct): ?>
                <li class="list-custom">
                    <p><?= ($direct) ?></p>
                </li>
            <?php endforeach; ?>

        </ol>
        <!--<div class="col-md-3 col-sm-6 mb-4">
            <h1>1.</h1>
            <p>Boil the spaghetti</p>
        </div>-->

    </div>
    <!-- /.row -->

</div>
<!-- /.container -->
