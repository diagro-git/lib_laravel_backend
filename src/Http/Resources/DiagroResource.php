<?php
namespace Diagro\Backend\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * header X-FIELDS=fieldname, fieldname, ....
 */
class DiagroResource extends JsonResource
{

    private array $fields = [];

    private int $countFields = 0;


    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->initFields();
    }


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