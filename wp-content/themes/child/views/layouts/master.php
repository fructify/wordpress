<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= $this->e(bloginfo('name')) ?> - <?= $this->v('title', function(){ return bloginfo('description'); }) ?></title>
        <link rel="stylesheet" href="<?= $this->assetUrl('styles.css') ?>">
        <?php wp_head(); ?>
    </head>
    <body>
        <div class="container">

            <header><h1>Fructify</h1><hr></header>

            <main><?= $this->supply('main') ?></main>

            <footer>
                <h6>Built By <a href="https://github.com/brad-jones/">Brad</a></h6>
            </footer>

        </div>
        <script src="<?= $this->assetUrl('script.js') ?>"></script>
        <?php wp_footer(); ?>
    </body>
</html>
