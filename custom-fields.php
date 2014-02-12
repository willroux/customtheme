

<?php $lieu = get_post_meta(get_the_ID(),'wpcf-lieu',true); 
$musiciens = get_post_meta(get_the_ID(),'wpcf-musiciens',true);
$beneficiaire = get_post_meta(get_the_ID(),'wpcf-beneficiaire',true);
$recette = get_post_meta(get_the_ID(),'wpcf-recette',true);
$lat= get_post_meta(get_the_ID(),'lat',true);
$lng= get_post_meta(get_the_ID(),'lng',true);
?>

<div class="meta">
<?php if(!empty($lieu)): ?>
	<span><strong>Lieu :</strong> <?php echo $lieu; ?> </span><br/>
<?php endif; ?>
	
<?php if(!empty($musiciens)): ?>
	<span><strong>Musiciens :</strong> <?php $musiciens=str_replace('.', '<br/>', $musiciens); 
	echo $musiciens;
	?> </span><br/>

<?php endif; ?>

<?php if(!empty($beneficiaire)): ?>
	<span><strong>Bénéficiaire : </strong><?php echo $beneficiaire; ?> </span>
	<br/><br/>
<?php endif; ?>

<?php if(!empty($recette)): ?>
	<span><strong>Recette : </strong><?php echo $recette; ?> </span>
	<br/><br/>
<?php endif; ?>

<?php if(!empty($lat)): ?>
	<span><strong>latitude :</strong> <?php echo $lat; ?> </span>
	<br/><br/>
<?php endif; ?>

<?php if(!empty($lng)): ?>
	<span><strong>longitude : </strong><?php echo $lng; ?> </span>
	<br/>
<?php endif; ?>

</div>
