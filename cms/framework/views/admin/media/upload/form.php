<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
?>
<form method="POST" action="admin/admin/media/upload/do" enctype="multipart/form-data">
	<input type="hidden" name="media_path_id" value="<?= $folder->medif_id ?>" />
	<p><label>Name: <input type="text" name="media_title" /></label></p>
	<p><label>Choose a file: <input type="file" name="media" /></label></p>
	<p><input type="submit" value="Upload" ></p>
</form>
<script type="text/javascript">
require(['jquery-nos', 'static/cms/js/jquery/jquery-form/jquery.form.min'], function($) {
	$('form').submit(function(e) {
		$(this).ajaxSubmit({
			dataType: 'json',
			success: function(json) {
				console.log(json);
				if (json.error) {
					$.nos.notify(json.error, 'error');
				}
				if (json.notify) {
					$.nos.notify(json.notify);
				}
				if (json.listener_fire) {
					$.nos.listener.fire(json.listener_fire, json.listener_bubble || true, json.listener_data);
				}
				if (json.redirect) {
					document.location = json.redirect;
				}

				// Close at the end!
				if (json.closeTab) {
					$.nos.tabs.close();
				}
				if (json.closeDialog) {
					window.parent.jQuery(':wijmo-wijdialog')
						.wijdialog('close')
						.wijdialog('destroy')
						.remove();
				}
			},
			error: function() {
				$.nos.notify('An error occured', 'error');
			}
		});
		e.preventDefault();
	});
});
</script>	