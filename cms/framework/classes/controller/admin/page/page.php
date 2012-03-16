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

class Controller_Admin_Page_Page extends Controller {

    public function action_add() {

        $parent = Model_Page_Page::find(1);
        $page   = Model_Page_Page::forge();

        $fields = \Config::load('cms::controller/admin/page/form_page', true);
        $fields = \Arr::merge($fields, array(
            'page_title' => array(
                'validation' => array('required'),
            ),
            'page_parent_id' => array(
                'form' => array(
                    'type' => 'hidden',
                    'value' => $parent->page_id,
                ),
            ),
			'save' => array(
				'form' => array(
					'value' => __('Add'),
				),
			),
        ));

		$fieldset = \Fieldset::build_from_config($fields, $page, array(
            'before_save' => function($page, $data) {
                $parent = $page->get_parent();
                // Event 'after_change_parent' will set the appropriate lang
                //\Debug::dump($parent->id, $parent->get_lang(), $page->get_lang());
                //\Debug::dump($parent->find_lang('en_GB')->id);
                $page->set_parent($parent);
                $page->page_level = $parent->page_level + 1;
            },
            'success' => function() use ($page) {
                return array(
                    'notify' => 'Page sucessfully created.',
                    'fireEvent' => array(
                        'event' => 'reload',
                        'target' => 'cms_page',
                    ),
                    'replaceTab' => 'admin/admin/page/page/edit/'.$page->page_id,
                );
            }
        ));
		$fieldset->js_validation();
        $fieldset->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>');

        return \View::forge('cms::admin/page/page_add', array(
			'parent'   => $parent,
			'page'     => $page,
			'fieldset' => $fieldset,
		), false);
    }

    public function action_edit($id) {

        $page = Model_Page_Page::find($id);

        $fields = \Config::load('cms::controller/admin/page/form_page', true);
        $fields = \Arr::merge($fields, array(
            'page_id' => array (
                'label' => 'ID: ',
                'widget' => 'text',
            ),
        ));

		$fieldset = \Fieldset::build_from_config($fields, $page, array(
            'success' => function() {
                return array(
                    'notify' => 'Page sucessfully saved.',
                    'fireEvent' => array(
                        'event' => 'reload',
                        'target' => 'cms_page',
                    ),
                );
            }
        ));
		$fieldset->js_validation();
        $fieldset->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>');

        return \View::forge('cms::admin/page/page_edit', array(
			'page'     => $page,
			'fieldset' => $fieldset,
		), false);
    }

    protected static function  _get_page_with_permission($page_id, $permission) {
        if (empty($page_id)) {
            throw new \Exception('No page specified.');
        }
        $page = Model_Page_Page::find($page_id);
        if (empty($page)) {
            throw new \Exception('Page not found.');
        }
        if (!static::check_permission_action('delete', 'controller/admin/page/mp3grid/list', $page)) {
            throw new \Exception('Permission denied');
        }
        return $page;
    }

	public function action_delete_page($page_id = null) {
        try {
            $page = static::_get_page_with_permission($page_id, 'delete');
            return \View::forge('cms::admin/page/page_delete', array(
                'page' => $page,
            ));
        } catch (\Exception $e) {
            // Easy debug
            if (\Fuel::$env == \Fuel::DEVELOPMENT && !\Input::is_ajax()) {
                throw $e;
            }
			$body = array(
				'error' => $e->getMessage(),
			);
            \Response::json($body);
		}
    }

	public function action_delete_page_confirm() {
        try {
            $page_id = \Input::post('id');
            // Allow GET for easier dev
            if (empty($page_id) && \Fuel::$env == \Fuel::DEVELOPMENT) {
                $page_id = \Input::get('id');
            }

            $page = static::_get_page_with_permission($page_id, 'delete');
            // Delete all languages by default
            $lang = \Input::post('lang', 'all');

            // Delete children for all languages
            if ($lang == 'all') {
                // Children will be deleted recursively (with the 'after_delete' event from the Tree behaviour)
                // Optimised operation for deleting all languages
                $page->delete_all_lang();
            } else {
                // Search for the appropriate page
                if ($lang != 'all' && $page->get_lang() != $lang) {
                    $page = $page->find_lang($lang);
                }

                if (empty($page)) {
                    throw new \Exception(strtr(__('The page has not been found in the requested language {language}'), array(
                        '{language}' => $lang,
                    )));
                }

                // Reassigns common_id if this item is the main language (with the 'after_delete' event from the Translatable behaviour)
                // Children will be deleted recursively (with the 'after_delete' event from the Tree behaviour)
                $page->delete();
            }

			$body = array(
				'notify' => 'Page successfully deleted.',
                'fireEvent' => array(
	                'event' => 'reload',
                    'target' => 'cms_page',
                ),
			);

        } catch (\Exception $e) {
            // Easy debug
            if (\Fuel::$env == \Fuel::DEVELOPMENT && !\Input::is_ajax()) {
                throw $e;
            }
			$body = array(
				'error' => $e->getMessage(),
			);
		}

        \Response::json($body);
    }
}