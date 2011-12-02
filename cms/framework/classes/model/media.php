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

class Model_Media extends Model {
    protected static $_table_name = 'cms_media';
    protected static $_primary_key = array('media_id');

    public static $public_path = 'media/';

    protected static $_has_one = array(
        'path' => array(
            'key_from'       => 'media_path_id',
            'model_to'       => 'Cms\Model_Media_Path',
            'key_to'         => 'medip_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
    );

    /**
     * Properties
     * media_id
     * media_path_id
     * media_file
     * media_ext
     * media_title
     * media_module
     * media_protected
     * media_width
     * media_height
     */

    public function get_public_path() {
        //$this->_relate('path');
        return static::$public_path.$this->media_path.$this->media_file;
    }

    public function get_img_tag($params = array()) {
        if (!$this->is_image()) {
            return false;
        }
        if (!empty($params['max_width']) || !empty($params['max_height'])) {
            list($width, $height, $ratio) = Tools_Image::calculate_ratio($this->media_width, $this->media_height, $params['max_width'], $params['max_height']);
            $src = $this->get_public_path_resized($params['max_width'], $params['max_height']);
        } else {
            list($width, $height) = array($this->media_width, $this->media_height);
            $src = $this->get_public_path();
        }
        return '<img src="'.$src.'" width="'.$width.'" height="'.$height.'" />';
    }

    public function get_img_tag_resized($max_width = null, $max_height = null) {
        return $this->get_img_tag(array(
            'max_width'  => $max_width,
            'max_height' => $max_height,
        ));
    }

    public function is_image() {
        return array($this->media_ext, array('jpg', 'png', 'gif', 'jpg', 'bmp'));
    }

    public function get_public_path_resized($max_width = 0, $max_height = 0) {
        if (!$this->is_image()) {
            return false;
        }
        return str_replace('media/', 'cache/media/', static::$public_path).$this->media_path.str_replace('.'.$this->media_ext, '', $this->media_file).'/'.(int) $max_width.'-'.(int) $max_height.'.'.$this->media_ext;
    }
}
