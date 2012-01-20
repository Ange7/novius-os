<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms;

use Fuel\Core\Config;

class Controller_Admin_Media_Mode_Image extends Controller_Mp3table_List {

	public function before() {
		Config::load('cms::admin/media/media', true);
		$this->config = Config::get('cms::admin/media/media', array());
        $this->config['urljson'] = 'static/cms/js/admin/media/media_image.js';

        /*
		// Add the "Choose" action button
		if (isset($this->config['ui']['actions'])) {
			array_unshift($this->config['ui']['actions'], array(
				'label' => 'Choose',
				'action'   =>  'function(item) {
					console.log(this);
					console.log(item);
					$.nos.listener.fire("media.pick", true, [item]);
				}')
			);
		}


		// Remove the choices for the extension
		foreach ($this->config['ui']['inspectors'] as $id => $inspector) {
			if ($inspector['widget_id'] == 'inspector-extension') {
				unset($this->config['ui']['inspectors'][$id]);
			}
		}
        */

		// Force only images to be displayed
		$this->config['ui']['values'] = array(
			'media_extension' => array('image'),
		);

		parent::before();
	}
}
