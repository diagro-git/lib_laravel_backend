<?php
namespace Diagro\Backend\Http;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Use this class if you want custom FormRequest rules to be based
 * on the controllers action name.
 *
 * This can be used if other rules are used in different requests.
 * For example a create can have 5 rules but when updating, some rules are optional.
 * Then you have a create() and update() method which return a different set of rules.
 *
 * De defaultRules() method returns default rules to be used when no action is found.
 * This returns an empty array by default.
 *
 * You can set this as default rules and then extract some rules with the Arr helper.
 * For example a default rule set can be ['name' => ..., 'email' => ....]
 * But the update method only needs the name rule:
 * return Arr::only($this->defaultRules(), 'name');
 *
 * Poof, you didn't type the name rule twice!
 *
 * @package Diagro\Backend\Http
 */
abstract class ActionMethodRulesRequest extends FormRequest
{


    /**
     * Get the default validation rules that apply to the request.
     * Only used when no action method is found in this class.
     *
     * @return array
     */
    protected function defaultRules() : array
    {
        return [];
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        $action = $this->route()->getActionMethod();
        if(method_exists($this, $action)) {
            return $this->{$action}();
        }

        return $this->defaultRules();
    }


}