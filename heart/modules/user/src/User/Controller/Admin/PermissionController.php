<?php

namespace User\Controller\Admin;

class PermissionController extends \AdminController
{
    /**
     * Before function for PermissionController
     *
     * @return void
     **/
    public function before()
    {
        $this->menu->activeParent('user');
        $this->template->header = \Translate::get('user::permission.title');
    }

    /**
    * Get groups to edit permissions for each group
    *
    * @return void
    */
    public function index()
    {
        if (!user_has_access('user.permission')) return $this->notFound();
        $group = \UserGroup::all();

        $this->template->title(\Translate::get('user::permission.title'))
                ->breadcrumb(\Translate::get('user::permission.title'))
                ->set('group', $group)
                ->setPartial('admin/permission/index');
    }

    /**
    * Edit permissions for each usergroup
    *
    * @param int $groupid
    * @return void
    */
    public function edit($groupid = null)
    {
        if (!user_has_access('user.permission.edit') or is_null($groupid)) return $this->notFound();
        $group = \UserGroup::findBy('id', $groupid);

        if (!$group) {
            return \Redirect::to('admin/user/permission');
        }

        // Get permission from the installed modules
        $permission_modules = \Module::getAll();
        unset($permission_modules['admin']);

        if (\Input::isPost()) {

            $modules = \Input::get('modules');
            $actions = \Input::get('modules_actions');

            if (!is_null($modules)) {
                $module_lists = array();
                foreach ($modules as $k => $v) {
                    $modules[$k] = 1;
                    foreach ($permission_modules as $m) {
                        if (empty($modules[$m['uri']])) {
                            $modules[$m['uri']] = 0;
                        }
                        $module_lists[$m['uri']] = $m['uri'];
                    }
                }

                // Add Module Actions Permission
                if (!is_null($actions)) {
                    foreach ($group->permissions as $k => $v) {
                        if ($k == 'admin') {
                            continue;
                        }
                        if (!array_key_exists($k, $actions)
                            and !array_key_exists($k, $module_lists)) {
                            $modules[$k] = 0;
                        }
                    }

                    foreach ($actions as $k => $v) {
                        $modules[$k] = (int) $v;
                    }
                }

                $group->permissions = $modules;

                if ($group->save()) {
                   \Flash::success(\Translate::get('user::permission.save'));

                    return \Redirect::to('admin/user/permission/');
                } else {
                    \Flash::success(\Translate::get('user::permission.error'));

                    return \Redirect::to('admin/user/permission/');
                }
            }
        }

        $groupPermissions = $group->getPermissions();

        $this->template->title(\Translate::get('user::permission.title'))
                ->breadcrumb(\Translate::get('user::permission.title'))
                ->set('groupPermissions', $groupPermissions)
                ->set('permission_modules', $permission_modules)
                ->set('group', $group)
                ->script('user.js', 'user', 'footer')
                ->setPartial('admin/permission/edit');
    }
}
