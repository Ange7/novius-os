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

class Controller_Admin_User_List extends Controller_Mp3table_List {

    public function before($response = null) {
        Config::load('cms::admin/user/user', true);
        $this->config = \Config::getFromUser('cms::admin/user/user', array());

        parent::before($response);
    }
}
