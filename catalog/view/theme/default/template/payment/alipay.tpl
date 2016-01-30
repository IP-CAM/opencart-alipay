<div class="buttons">
  <div class="pull-right">
    <a target="_blank" href="<?php echo $submit; ?>"><input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" /></a>
  </div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').on('click', function() {

	$.ajax({ 
		type: 'get',
		url: 'index.php?route=payment/alipay/confirm',
		cache: false,
		beforeSend: function() {
			$('#button-confirm').button('loading');
		},
		complete: function() {
			$('#button-confirm').button('reset');
		},		
		success: function() {
			location = '<?php echo $continue; ?>';
			 
		}		
	});
	
	 
});
 
</script> 
