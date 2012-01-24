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
<div id="<?= $uniqid = uniqid(); ?>">
	<h1 class="title"><?= $logged_user->user_fullname; ?></h1>

	<a href="admin/tray/account/disconnect"><button>Disconnect</button></a>

	<div id="tabs" style="width: 100%;">
		<ul style="width: 15%;">
			<li><a href="#infos"><?= __('Informations') ?></a></li>
			<li><a href="#password"><?= __('Password') ?></a></li>
			<li><a href="#display"><?= __('Display') ?></a></li>
		</ul>
		<div id="infos" style="width: 80%;">
			<?= $fieldset_infos ?>
		</div>
		<div id="password" style="width: 80%;">
			<?= $fieldset_password ?>
		</div>
		<div id="display" style="width: 80%;">
			<?= $fieldset_display ?>
		</div>
	</div>
</div>

<script type="text/javascript">
    require(['jquery-nos'], function($) {
		$.nos.ui.form('#<?= $uniqid ?>');
        require(['static/cms/js/jquery/wijmo/js/jquery.wijmo.wijtabs.js'],
            function() {
                $('#tabs').wijtabs({
                    alignment: 'left'
                });
            }
        );
    });
</script>