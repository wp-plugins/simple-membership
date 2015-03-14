<script>
jQuery(document).ready(function($){
	$('#member_since').dateinput({'format':'yyyy-mm-dd',selectors: true,yearRange:[-100,100]});
	$('#subscription_starts').dateinput({'format':'yyyy-mm-dd',selectors: true,yearRange:[-100,100]});
});
</script>
