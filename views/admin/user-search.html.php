<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div id="searchResults">
      <?php if (! $people) : ?>
        <h3>No people matched the search criteria.</h3>
      <?php endif; ?>
      <?php foreach ($people as $person) : ?>
        <hr />
        <a href="/admin/users/<?= $this->esc($person['person_id']) ?>">
        <?php
          include __DIR__ . DIRECTORY_SEPARATOR . 'person.html.php';
        ?>
        </a>
      <?php endforeach; ?> 
    </div><!-- searchResults -->
  </div><!-- container -->
</body>
</html>
