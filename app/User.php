<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Sentinel;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function allUsers($data)
    {
        $default = [
            'search' => '',
            'orderby' => 'id',
            'order' => 'desc',
            'role' => '',
            'number' => posts_per_page(),
            'page' => 1
        ];
        $data = wp_parse_args($data, $default);

        $sql = DB::table($this->getTable())->selectRaw('users.*, roles.slug as role_slug, roles.name as role_name')->join('role_users', 'users.id', '=', 'role_users.user_id', 'inner')
            ->join('roles', 'role_users.role_id', '=', 'roles.id', 'inner');
        $number = $data['number'];
        if (!empty($number)) {
            $offset = ($data['page'] - 1) * $number;
            $sql->limit($number)->offset($offset);
        }

        if (!empty($data['search'])) {
            $search = esc_sql($data['search']);
            if (is_numeric($data['search'])) {
                $sql->where('users.id', $search);
            } else {
                $sql->whereRaw("(users.email LIKE '%{$search}%' OR users.first_name LIKE '%{$search}%' OR users.last_name LIKE '%{$search}%')");
            }
        }
        if (!empty($data['role'])) {
            $sql->where('roles.id', $data['role']);
        }

        $sql->orderBy($data['orderby'], $data['order']);

        $results = $sql->get();
        $count = DB::select("SELECT FOUND_ROWS() as `row_count`")[0]->row_count;

        return [
            'total' => $count,
            'results' => $results
        ];
    }

    public function getUserRole($user_id)
    {
        $sql = DB::table('roles')->selectRaw('roles.id, roles.slug, roles.name')->join('role_users', 'roles.id', '=', 'role_users.role_id')->where('role_users.user_id', $user_id);
        $result = $sql->get()->first();
        return (is_object($result)) ? $result : null;
    }

    public function updateUser($user_id, $data)
    {
        return DB::table($this->getTable())->where('id', $user_id)->update($data);
    }

    public function updateUserRole($user_id, $role_id)
    {
        DB::table('role_users')->where('user_id', $user_id)->delete();
        $user = get_user_by_id($user_id);
        $role = Sentinel::findRoleById($role_id);
        if ($role && $user) {
            $role->users()->attach($user);
            return true;
        }
        return false;
    }

    public function updateUserMeta($user_id, $meta_key, $meta_value = '')
    {
        if (is_array($meta_key)) {
            foreach ($meta_key as $key => $value) {
                DB::table('usermeta')->updateOrInsert(['user_id' => $user_id, 'meta_key' => $key], ['meta_value' => maybe_serialize($value)]);
            }
        } else {
            return DB::table('usermeta')->updateOrInsert(['user_id' => $user_id, 'meta_key' => $meta_key], ['meta_value' => maybe_serialize($meta_value)]);
        }
    }

    public function getRoleByName($role_name = 'customer')
    {
        $role = DB::table('roles')->where('slug', $role_name)->get()->first();
        return (is_object($role)) ? $role : null;
    }

    public function getUserMeta($user_id, $meta_key)
    {
        return DB::table('usermeta')->where('user_id', $user_id)->where('meta_key', $meta_key)->get()->first();
    }

    public function getAllRoles()
    {
        $results = DB::table('roles')->get();
        return (is_object($results) && $results->count()) ? $results : null;
    }
}
