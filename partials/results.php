<?php if (isset($exception)) : ?>
  <div class="error">
    <?php echo $exception->getMessage() ?>
  </div>
<?php endif; ?>

<?php if (isset($benchmark)) : ?>
  <div class="results">
    <ul>
      <?php if ($benchmark->getUrl()) : ?>
        <li><strong>URL:</strong> <?php echo $benchmark->getUrl() ?></li>
      <?php endif; ?>

      <?php if ($benchmark->getContentType()) : ?>
        <li><strong>Content Type:</strong> <?php echo $benchmark->getContentType() ?></li>
      <?php endif; ?>

      <?php if ($benchmark->getNumberOfBytes()) : ?>
        <li><strong>Number of Bytes:</strong> <?php echo number_format($benchmark->getNumberOfBytes()) ?></li>
      <?php endif; ?>

      <?php if ($benchmark->getTotalDownloadTime()) : ?>
        <li><strong>Total Download Time:</strong> <?php echo number_format($benchmark->getTotalDownloadTime(), 3) ?> seconds</li>
      <?php endif; ?>
    </ul>
  </div>
<?php endif; ?>