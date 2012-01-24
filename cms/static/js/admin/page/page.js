/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

define([
    'jquery-nos'
], function($) {
    var mp3Grid = $.nos.mp3GridSetup(),
        actions = {
            edit : {
                label : mp3Grid.i18n('Edit'),
                action : function(item) {
                    $.nos.tabs.add({
                        iframe : true,
                        url : 'admin/admin/page/form/edit/' + item.id,
                        label : item.title
                    });
                }
            }
        };

    return $.extend(true, mp3Grid, {
        tab : {
            label : mp3Grid.i18n('Pages'),
            iconUrl : 'static/cms/img/32/page.png'
        },
        mp3grid : {
            adds : {
                page : {
                    label : mp3Grid.i18n('Add a Page'),
                    url : 'admin/admin/page/page/add'
                },
                root : {
                    label : mp3Grid.i18n('Add a root'),
                    url : 'admin/admin/page/root/add'
                }
            },
            grid : {
                proxyUrl : 'admin/admin/page/list/json',
                columns : {
                    title : {
                        headerText : mp3Grid.i18n('Title'),
                        dataKey : 'title'
                    },
                    url : {
                        headerText : mp3Grid.i18n('Virtual url'),
                        dataKey : 'url'
                    },
                    actions : {
                        actions : [
                            actions.edit
                        ]
                    }
                }
            },
            inspectors : {
                roots : {
                    widget_id : 'cms_page_roots',
                    vertical : true,
                    label : mp3Grid.i18n('Roots'),
                    url : 'admin/admin/page/inspector/root/list',
                    inputName : 'rac_id',
                    grid : {
                        urlJson : 'admin/admin/page/inspector/root/json',
                        columns : {
                            title : {
                                headerText : mp3Grid.i18n('Root'),
                                dataKey : 'title'
                            }
                        }
                    }
                },
                directories : {
                    widget_id : 'cms_page_directories',
                    vertical : true,
                    label : mp3Grid.i18n('Directories'),
                    url : 'admin/admin/page/inspector/tree/list',
                    inputName : 'directory_id',
                    grid : {
                        urlJson : 'admin/admin/page/inspector/tree/json',
                        columns : {
                            title : {
                                headerText : mp3Grid.i18n('Directory'),
                                dataKey : 'title'
                            }
                        }
                    }
                }
            }
        }
    });
});
