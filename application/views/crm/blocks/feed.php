<?php foreach ($directories as $directory): ?>


    <tr class="dir-row">
        <td data-th="<?= lang('name') ?>" class="p-b10">
            <a href="<?php echo site_url('crm/directory/'.$directory->id); ?>" class="link"><?php echo $directory->username; ?></a>
        </td>
        <td data-th="<?= lang('company') ?>" class="p-b10">
            <?php echo $directory->company; ?>
        </td>
        <td data-th="<?= lang('action') ?>" class="p-b10">
            <a href="<?php echo site_url('crm/edit/'.$directory->id); ?>" class="link m-r10"><i class="fa fa-pencil-square-o"></i> <?= lang('edit') ?></a>
            <a href="<?php echo site_url('crm/delete/'.$directory->id); ?>" class="remove_link"><i class="fa fa-remove"></i> <?= lang('remove') ?></a>
        </td>
    </tr>

<?php endforeach; ?>