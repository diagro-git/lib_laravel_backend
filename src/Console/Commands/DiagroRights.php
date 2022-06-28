<?php
namespace Diagro\Backend\Console\Commands;


use App\Models\Application;
use App\Models\ApplicationRight;
use App\Models\ApplicationRole;
use App\Models\ARAR;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class DiagroRights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagro:rights';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registreer de rechten en ken de rechten toe aan de gebruikte rollen.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $config = config('diagro');
        $appId = $config['app_id'];
        /** @var Application $application */
        $application = Application::query()->withoutGlobalScope('companyVisibility')->findOrFail($appId);

        //update rights
        $this->updateApplicationRights($application, $config['rights']);

        //update used roles
        $this->updateApplicationRoles($application, array_keys($config['roles']));

        //update role permission
        $this->updateRolePermissions($application, $config['rights'], $config['roles']);
    }

    private function updateApplicationRights(Application $application, array $rights)
    {
        foreach($rights as $name => $description) {
            $right = ApplicationRight::query()->firstOrCreate([
                'application_id' => $application->id,
                'name' => $name
            ], [
                'description' => $description
            ]);
            if($right->description != $description) {
                $right->update(['description' => $description]);
            }
        }
    }

    private function updateApplicationRoles(Application $application, array $roles)
    {
        foreach($roles as $name) {
            if($name === '*') continue;

            $role = Role::query()->firstOrCreate([
                'name' => $name
            ]);
            if($application->roles()->where('roles.id', '=', $role->id)->doesntExist()) {
                $application->roles()->attach($role->id);
            }
        }
    }

    private function updateRolePermissions(Application $application, array $rights, array $roles)
    {
        foreach($roles as $name => $permissions) {
            if($name === '*') {
                $arr = [];
                Role::query()
                    ->whereNotIn('name', array_keys($roles))
                    ->get()
                    ->each(function(Role $role) use (&$arr, $permissions) {
                        $arr[$role->name] = $permissions;
                    });
                $this->updateRolePermissions($application, $rights, $arr);
            } else {
                /** @var ApplicationRole $role */
                $role = ApplicationRole::query()->where('application_id', '=', $application->id)->whereHas('role', function($query) use($name) {
                    $query->where('name', '=', $name);
                })->firstOrFail();
                $rightNames = array_keys($rights);

                //delete permissions that aren't used
                if(empty($permissions)) {
                    $role->arar()->delete();
                } elseif(! in_array('*', array_keys($permissions))) {
                    $unusedRightNames = array_keys(Arr::except($rights, array_keys($permissions)));
                    ARAR::query()->where('application_role_id', '=', $role->id)->whereHas('applicationRight', function($query) use($unusedRightNames) {
                        $query->whereIn('name', $unusedRightNames);
                    })->delete();
                }

                //insert permissions
                foreach($permissions as $rightName => $permission) {
                    if($rightName === '*') {
                        $usedNames = array_keys(Arr::where($permissions, fn($name, $permission) => $name != '*'));
                        $arr = [];
                        collect($rightNames)->each(function($rightName) use(&$arr, $permission, $usedNames) {
                            if(! in_array($rightName, $usedNames)) {
                                $arr[$rightName] = $permission;
                            }
                        });
                        $this->updateRolePermissions($application, $rights, [$name => $arr]);
                    } elseif(in_array($rightName, $rightNames)) {
                        $this->updateARAR(
                            ApplicationRight::query()->where('application_id', '=', $application->id)->where('name', '=', $rightName)->firstOrFail(),
                            $role,
                            $permission
                        );
                    }
                }
            }
        }
    }

    private function updateARAR(ApplicationRight $right, ApplicationRole $role, string $permissions)
    {
        $arar = ARAR::query()->withTrashed()->firstOrCreate([
            'application_role_id' => $role->id,
            'application_right_id' => $right->id,
        ]);

        //restore if it's soft deleted
        if($arar->trashed()) {
            $arar->restore();
        }

        //reset every permission to false
        $arar->denyAll();
        //every match is set to true
        foreach(str_split($permissions) as $permission) {
            if($permission === '*') {
                $arar->allowAll();
            } elseif($permission === 'r') {
                $arar->read = true;
            } elseif($permission === 'c') {
                $arar->create = true;
            } elseif($permission === 'u') {
                $arar->update = true;
            } elseif($permission === 'd') {
                $arar->delete = true;
            } elseif($permission === 'p') {
                $arar->publish = true;
            } elseif($permission === 'e') {
                $arar->export = true;
            }
        }

        $arar->saveOrFail();
    }
}
