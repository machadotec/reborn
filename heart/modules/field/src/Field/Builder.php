<?php

namespace Field;

use Event;
use Config;
use Input;
use Field\Model\Field as FieldModel;
use Field\Model\FieldData as FieldDataModel;
use Field\Model\FieldGroup as FieldGroupModel;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Field Builder Class
 *
 * @package Field
 * @author Nyan Lynn Htut
 **/
class Builder
{

    /**
     * Supported field type variable
     *
     * @var array
     **/
    protected $support_type = array();

    /**
     * Extended field type variable
     *
     * @var array
     **/
    protected $extend_type = array();

    /**
     * Merge of Supported and Extended type variable
     *
     * @var array
     **/
    protected $all_type = array();

    /**
     * Group Model
     *
     * @var \Reborn\MVC\Model|null
     **/
    protected $group;

    /**
     * Application (IOC) Instance
     *
     * @var \Reborn\Cores\Application
     **/
    protected $app;

    /**
     * Construct mothod
     *
     * @param  array $fields Custom Fields
     * @return void
     **/
    public function __construct()
    {
        $this->app = \Facade::getApplication();

        // Register Supported Field Types
        $this->registerSupprotedType();

        // Call Extended Field Type Event
        $this->extednedFieldType();

        // Merge Field type
        $this->mergeFieldTypes();
    }

    /**
     * Get the Supported Field Type
     *
     * @return array
     **/
    public function mergeFieldTypes()
    {
        $this->all_type = array_merge($this->support_type, $this->extend_type);
    }

    /**
     * Get all supported type
     *
     * @return array
     **/
    public function getFieldTypes()
    {
        return $this->all_type;
    }

    /**
     * Get Type Form for Field Create
     *
     * @param  string      $type    Field type
     * @param  string      $default Default value of field
     * @param  string      $options Options value of field
     * @return string|null
     **/
    public function getTypeForm($type, $default = null, $options = null)
    {
        if ($ins = $this->getTypeInstance($type)) {
            return $ins->filler($default, $options);
        }

        return null;
    }

    /**
     * Get Field Form for Group
     *
     * @param  string $relation Relation name for group
     * @return string
     **/
    public function getForm($relation, $model = null)
    {
        $group = $this->getGroupData($relation);

        // Empty Group for $relation, return empty array()
        if(is_null($group) || empty($group->fields)) return array();

        $fields = FieldModel::whereIn('id', $group->fields)->get();

        if (! is_null($model)) {
            $field_data = $this->getFieldValue((int) $group->id, (int) $model->id);
        } else {
            $field_data = null;
        }

        $form = array();

        $builder = $this;
        $fields->each(function ($f) use (&$form, $field_data, $builder) {
            if ($ins = $builder->getTypeInstance($f->field_type)) {
                $data = isset($field_data[$f->id]) ? $field_data[$f->id] : null;

                $form[$f->id] = $ins->displayForm($f, $data);
            }
        });

        // Make Sorting by $group->fields
        $makeSorting = function ($array, $orderArray) {
            $ordered = array();
            foreach ($orderArray as $key) {
                if (array_key_exists($key,$array)) {
                    $ordered[$key] = $array[$key];
                    unset($array[$key]);
                }
            }

            return $ordered + $array;
        };
        $form = $makeSorting($form, $group->fields);

        return $form;
    }

    /**
     * Save External Field
     *
     * @param  string            $realtion Relational Name
     * @param  \Reborn\MVC\Model $model    Original Module's Model
     * @return void**
     **/
    public function save($relation, $model)
    {
        $group = $this->getGroupData($relation);

        if(is_null($group) || empty($group->fields)) return true;

        $fields = FieldModel::whereIn('id', $group->fields)->get();

        $inserts = array();

        $group_id = (int) $group->id;
        $model_id = (int) $model->id;

        $field_data = $this->getFieldValue($group_id, $model_id);
        $builder = $this;
        $fields->each(function ($f) use (&$inserts, $group_id, $model_id, $builder) {
            if ($ins = $builder->getTypeInstance($f->field_type)) {
                if ( $val = $ins->preSaveCheck($f) ) {
                    $inserts[] = array(
                            'field_id' => (int) $f->id,
                            'post_id' => $model_id,
                            'group_id' => $group_id,
                            'field_name' => $f->field_name,
                            'field_value' => $val
                        );
                }
            }
        });

        return \DB::table('field_data')->insert($inserts);
    }

    /**
     * Update External Field
     *
     * @param  string            $realtion Relational Name
     * @param  \Reborn\MVC\Model $model    Original Module's Model
     * @return void**
     **/
    public function update($relation, $model)
    {
        $group = $this->getGroupData($relation);

        if(is_null($group) || empty($group->fields)) return true;

        $fields = FieldModel::whereIn('id', $group->fields)->get();

        $group_id = (int) $group->id;
        $model_id = (int) $model->id;

        $inserts = array();

        $field_data = $this->getFieldValue($group_id, $model_id);

        $builder = $this;
        $fields->each(function ($f) use (&$inserts, $group_id, $model_id, $field_data, $builder) {
            if ($ins = $builder->getTypeInstance($f->field_type)) {
                $data = isset($field_data[$f->id]) ? $field_data[$f->id] : null;
                if (is_null($data)) {
                    // Need to save for new data
                    if ( $val = $ins->preSaveCheck($f) ) {
                        $inserts[] = array(
                                'field_id' => (int) $f->id,
                                'post_id' => $model_id,
                                'group_id' => $group_id,
                                'field_name' => $f->field_name,
                                'field_value' => $val
                            );
                    }
                } elseif ( $val = $ins->preUpdateCheck($f, $data) ) {

                    \DB::table('field_data')
                                ->where('group_id', $group_id)
                                ->where('post_id', $model_id)
                                ->where('field_id', (int) $f->id)
                                ->where('field_name', $f->field_name)
                                ->update(array('field_value' => $val));
                }
            }
        });

        if (empty($inserts)) {
            return true;
        } else {
            return \DB::table('field_data')->insert($inserts);
        }
    }

    /**
     * Delete Field
     *
     * @param  string            $realtion Relational Name
     * @param  \Reborn\MVC\Model $model    Original Module's Model
     * @return void
     **/
    public function delete($relation, $model)
    {
        $group = $this->getGroupData($relation);

        if(is_null($group) || empty($group->fields)) return true;

        $group_id = (int) $group->id;
        $model_id = (int) $model->id;

        return \DB::table('field_data')
                        ->where('group_id', $group_id)
                        ->where('post_id', $model_id)
                        ->delete();
    }

    /**
     * Get Fields For Single Model Object
     *
     * @param  string            $realtion Relational Name
     * @param  \Reborn\MVC\Model $model    Original Module's Model
     * @return \Reborn\MVC\Model
     **/
    public function get($relation, $model, $key = 'extended_fields')
    {
        // Check Model is Collection
        // If Model is instance of Collection
        // return with getAll method
        if ($model instanceof EloquentCollection) {
            return $this->getAll($relation, $model, $key);
        }

        $group = $this->getGroupData($relation);

        if(is_null($group) || is_null($model)) return $model;

        $group_id = (int) $group->id;

        $fields = FieldDataModel::where('post_id', $model->id)
                                    ->where('group_id', $group_id)
                                    ->get()->toArray();
        $data = array();
        foreach ($fields as $f) {
            $event = 'field.'.$f['field_name'].'.value.hook';

            $original = htmlspecialchars_decode($f['field_value']);

            if (\Event::has($event)) {

                $value = \Event::first($event, array($f['field_value']));

                if (is_null($value)) {
                    $value = $original;
                }
                $original = $value;
            }

            $data[$f['field_name']] = $original;
        }

        $model->{$key} = new \Field\Model\Data($data);

        return $model;
    }

    /**
     * Get Fields For Model Object Collection
     *
     * @param  string                                   $realtion   Relational Name
     * @param  \Illuminate\Database\Eloquent\Collection $collection
     * @return \Illuminate\Database\Eloquent\Collection
     **/
    public function getAll($relation, $collection, $key = 'extended_fields')
    {
        $group = $this->getGroupData($relation);

        if(is_null($group)) return $collection;

        $group_id = (int) $group->id;

        $collection->each(function (&$m) use ($group_id, $key) {

            $fields = FieldDataModel::where('post_id', $m->id)
                                    ->where('group_id', $group_id)
                                    ->get()->toArray();
            $data = array();
            foreach ($fields as $f) {
                $data[$f['field_name']] = htmlspecialchars_decode($f['field_value']);
            }
            $m->{$key} = $data;
        });

        return $collection;
    }

    /**
     * Get Field Type Instance
     *
     * @param  string                $type Field type
     * @return null|Field\Type\$type
     **/
    public function getTypeInstance($type)
    {
        $types = $this->all_type;

        if(!isset($types[$type])) return null;

        return new $types[$type]($this->app);
    }

    /**
     * Get Field Group Data
     *
     * @param  string          $relation Relational name
     * @return FieldGroupModel
     **/
    protected function getGroupData($relation)
    {
        if (!is_null($this->group)) {
            return $this->group;
        }

        $modules = module_select(false, false);

        $type = array_key_exists($relation, $modules) ? 'module' : 'content';

        $this->group = $group = FieldGroupModel::where('relation', $relation)
                                ->where('relation_type', $type)->first();

        return $group;
    }

    /**
     * Get Field Value with [id => value].
     *
     * @param  int   $group_id FieldData group_id
     * @param  int   $post_id  FieldData post_id
     * @return array
     **/
    protected function getFieldValue($group_id, $post_id)
    {
        $data = FieldDataModel::where('post_id', $post_id)
                            ->where('group_id', $group_id)
                            ->get()->toArray();
        $result = array();

        foreach ($data as $val) {
            $result[$val['field_id']] = $val['field_value'];
        }

        return $result;
    }

    /**
     * Register Supported Field Types
     *
     * @return void
     **/
    protected function registerSupprotedType()
    {
        $this->support_type = Config::get('field::field.support');
    }

    /**
     * Make etended field type.
     * Everybody can extend field type with Event::custom_field.extended.
     * example :
     * Event::on('custom_field.extended', function () {
     * 		return array('wysiwyg' => '\My\FieldType\Wysiwyg');
     * });
     *
     * @return void
     **/
    protected function extednedFieldType()
    {
        $fields = Event::call('custom_field.extended');

        foreach ($fields as $type => $class) {
            $this->extend_type[$type] = $class;
        }
    }

} // END class Builder
