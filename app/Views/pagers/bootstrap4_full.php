<?php $pager->setSurroundCount(2); ?>
<nav aria-label="<?= lang('Pager.pageNavigation') ?>">
  <ul class="pagination justify-content-center mb-0">
    <?php if ($pager->hasPrevious()) : ?>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getFirst() ?>" aria-label="<?= lang('Pager.first') ?>">&laquo;&laquo;</a>
      </li>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getPreviousPage() ?>" aria-label="<?= lang('Pager.previous') ?>">&laquo;</a>
      </li>
    <?php endif ?>

    <?php foreach ($pager->links() as $link) : ?>
      <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
        <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
      </li>
    <?php endforeach ?>

    <?php if ($pager->hasNext()) : ?>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getNextPage() ?>" aria-label="<?= lang('Pager.next') ?>">&raquo;</a>
      </li>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getLast() ?>" aria-label="<?= lang('Pager.last') ?>">&raquo;&raquo;</a>
      </li>
    <?php endif ?>
  </ul>
</nav>
