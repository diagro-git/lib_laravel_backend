<?php
namespace Diagro\Backend\Http\Resources;

trait DiagroResourceFields
{


    protected array $fields = [];

    protected int $countFields = 0;


    protected function field($name, $value)
    {
        return $this->when($this->countFields == 0 || in_array($name, $this->fields), $value);
    }


    protected function initFields()
    {
        $fields = request()->header('x-fields');
        if(! empty($fields)) {
            $fields = explode(',', $fields);
            foreach($fields as $k => $v) {
                $fields[$k] = trim($v);
            }

            $this->fields = $fields;
            $this->countFields = count($fields);
        }
    }


}