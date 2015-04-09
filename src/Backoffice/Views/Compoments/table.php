<?php if (!empty($tabElements)) : ?>


<table id="<?php echo $TabId ?>" class='globaltable <?php if (isset($tabClasses)) {
    echo $tabClasses;
} ?>'>
<caption class="caption title"><?php echo $tabCaption ?></caption>
    <thead>
        <tr>
            <?php foreach ($tabElements[0] as $key => $value):?>
            <th class="cell cell-<?php echo $key ?>"><?php echo $key ?></td>
            <?php endforeach; ?>
        </tr>
   </thead>


   <tbody>
       <?php foreach ($tabElements as $element):?>
       <tr>
            <?php foreach ($element as $key => $value):?>
            <td class="cell"><?php echo $value ?></td>
            <?php endforeach; ?>
       </tr>
       <?php endforeach; ?>
   </tbody>
</table>
<?php endif; ?>
