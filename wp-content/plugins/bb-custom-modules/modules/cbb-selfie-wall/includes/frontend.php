<?php
// echo "<pre>"; print_r($settings); echo "</pre>";
?>

<?php if ($settings->items) : ?>
<div class="cbb-selfie-wall">
    <span class="cbb-selfie-wall__mobile-msg"><?php echo __('Tap each photo to view details.', 'sage'); ?></span>
    <ul class="cbb-selfie-wall__grid">
        <?php foreach ($settings->items as $selfie) : ?>
            <li class="cbb-selfie-wall__grid-item" tabindex="0">
                <figure>
                    <img src="<?php echo $selfie->image_src; ?>" alt="" />
                    <figcaption>
                        <strong><?php echo $selfie->first_name; ?></strong>
                        <em><?php echo $selfie->job_role; ?></em>
                        <span><?php echo $selfie->location; ?></span>
                    </figcaption>
                </figure>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
