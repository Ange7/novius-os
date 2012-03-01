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

class Controller_Admin_Media_Upload extends \Controller {

	public function action_form($folder_id = null) {

        // Find root folder ID
        if (empty($folder_id)) {
            $query = Model_Media_Folder::find();
            $query->where(array('medif_parent_id' => null));
            $root = $query->get_one();
            $folder_id = $root->medif_id;
            $hide_widget_media_path = false;
        } else {
            $hide_widget_media_path = true;
        }

		$folder = Model_Media_Folder::find($folder_id);
        $fieldset = \Fieldset::build_from_config(array(
            'media_path_id' => array(
                'widget' => $hide_widget_media_path ? null : 'media_folder',
                'form' => array(
                    'type'  => 'hidden',
                    'value' => $folder->medif_id,
                ),
                'label' => __('Choose a folder where to put your media:'),
            ),
            'media' => array(
                'form' => array(
                    'type' => 'file',
                ),
                'label' => __('File from your hard drive: '),
            ),
            'media_title' => array(
                'form' => array(
                    'type' => 'text',
                ),
                'label' => __('Title: '),
            ),
            'slug' => array(
                'form' => array(
                    'type' => 'text',
                ),
                'label' => __('Slug: '),
            ),
            'save' => array(
                'form' => array(
                    'type' => 'submit',
                    'class' => 'primary',
                    'value' => __('Add'),
                    'data-icon' => 'circle-plus',
                ),
            ),
        ));

		return \View::forge('cms::admin/media/upload/form', array(
            'fieldset' => $fieldset,
            'folder' => $folder,
            'hide_widget_media_path' => $hide_widget_media_path,
		), false);
	}

	public function action_do() {

        try {
            if (!is_uploaded_file($_FILES['media']['tmp_name'])) {
                throw new \Exception(__('Please pick a file from your hard drive.'));
            }

            $pathinfo = pathinfo(strtolower($_FILES['media']['name']));

            $disallowed_extensions = \Config::get('upload.disabled_extensions', array('php'));
            if (in_array($pathinfo['extension'], $disallowed_extensions)) {
                throw new \Exception(__('This extension is not allowed due to security reasons.'));
            }

            $media = new Model_Media_Media();

            $media->media_path_id = \Input::post('media_path_id', 1);
            $media->media_module  = \Input::post('media_module', null);

            $media->media_title = \Input::post('media_title', '');
            $media->media_file  = \Input::post('slug', '');

            // Empty title = auto-generated from file name
            if (empty($media->media_title)) {
                $media->media_title = static::pretty_title($pathinfo['basename']);
            }

            // Empty slug = auto-generated with title
            if (empty($media->media_file)) {
                $media->media_file  = $media->media_title;
            }
            if (!empty($pathinfo['extension'])) {
                $media->media_file .= '.'.$pathinfo['extension'];
            }

            if (false === $media->check_and_filter_slug()) {
                throw new \Exception(__('Generated slug was empty.'));
            }
            if (false === $media->refresh_path()) {
                throw new \Exception(__("The parent folder doesn't exists."));
            }

            $dest = APPPATH.$media->get_public_path();
            if (is_file($dest)) {
                throw new \Exception(__('A file with the same name already exists.'));
            }

            // Create the directory if needed
			$dest_dir = dirname($dest);
            $base_dir = APPPATH.\Cms\Model_Media_Media::$public_path;
            $remaining_dir = str_replace($base_dir, '', $dest_dir);
            // chmod  is 0777 here because it should be restricted with by the umask
			is_dir($dest_dir) or \File::create_dir($base_dir, $remaining_dir, 0777);

            if (!is_writeable($dest_dir)) {
                throw new \Exception(__('No write permission. This is not your fault, but rather a misconfiguration from the server admin. Tell her/him off!'));
            }

            // Move the file
            if (move_uploaded_file($_FILES['media']['tmp_name'], $dest)) {
                $media->save();
                chmod($dest, 0664);
            }

			$body = array(
				'notify' => 'File successfully added.',
				'closeDialog' => true,
				'listener_fire' => 'refresh.cms_media_media',
				'listener_bubble' => true,
			);
        } catch (\Exception $e) {
			$body = array(
				'error' => $e->getMessage(),
			);
		}

        \Response::json($body);
	}

    /**
     * @param string $file
     * @return string
     */
	protected static function pretty_title($file) {
		$file = substr($file, 0, strrpos($file, '.'));
		$file = preg_replace('`[\W_-]+`', ' ', $file);
		$file = \Inflector::humanize($file, ' ');
		return $file;
	}
}