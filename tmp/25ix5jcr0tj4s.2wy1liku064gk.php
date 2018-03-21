<div class="container">

    <br>

    <div class="jumbotron">
        <div class="container text-center">
            <h1 class="display-4">
                <img src="<?= ($BASE) ?>/assets/images/CCLogo.png" class="logo1" alt="CC-Logo">
                College Cuisine
                <img src="<?= ($BASE) ?>/assets/images/CCLogo.png" class="logo2" alt="CC-Logo">
            </h1>
            <p>Cheap, Good, Healthy Recipes for the Ramen Alternative!</p>
        </div>
    </div>

    <div>

        <h2>All Recipes:</h2>
        <h5>Sorted by Most Popular.</h5>

    </div>
    <div class="row">

            <?php foreach (($recipes?:[]) as $recipe): ?>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card card-custom text-center">
                        <img class="card-img-top img-size-limit rounded" src="<?= ($recipe['image']) ?>" alt="RecipeImg">
                        <div class="card-body">
                            <a href="<?= ($BASE) ?>/recipe/<?= ($recipe['recipeid']) ?>">
                                <h5 class="card-title"><?= ($recipe['name']) ?></h5>
                            </a>
                            <p class="card-text text-truncate width-limit"><?= ($recipe['descript']) ?></p>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

    </div>


</div>
